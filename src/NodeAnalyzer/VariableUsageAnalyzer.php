<?php

declare(strict_types=1);

namespace RectorLaravel\NodeAnalyzer;

use Illuminate\Database\Eloquent\Model;
use PhpParser\Node;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\ArrayDimFetch;
use PhpParser\Node\Expr\Assign;
use PhpParser\Node\Expr\Closure;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\NullsafeMethodCall;
use PhpParser\Node\Expr\NullsafePropertyFetch;
use PhpParser\Node\Expr\PropertyFetch;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Expression;
use PhpParser\Node\Stmt\Function_;
use PhpParser\NodeTraverser;
use PHPStan\Type\ObjectType;
use Rector\NodeNameResolver\NodeNameResolver;
use RectorLaravel\NodeVisitor\LoopAndClosureNestingNodeVisitor;
use RectorLaravel\ValueObject\VariableUsage;
use Webmozart\Assert\Assert;

use function in_array;
use function is_string;
use function spl_object_id;
use function strtolower;

/**
 * Reads how the variables of one function body are created, shared and used. Every question is
 * one small pass over the already flattened list of nodes.
 */
final readonly class VariableUsageAnalyzer
{
    /**
     * The Eloquent methods that build or load a persisted model instance of their own. `new`,
     * `make()`, `findOrNew()` and `firstOrNew()` are left out: an unsaved model has no row, so
     * `fresh()` returns null where `refresh()` returns the model untouched.
     *
     * @var list<string>
     */
    private const array MODEL_CONSTRUCTORS = [
        'create',
        'forcecreate',
        'find',
        'findorfail',
        'first',
        'firstorfail',
        'firstorcreate',
        'createorfirst',
        'updateorcreate',
        'sole',
    ];

    public function __construct(
        private NodeNameResolver $nodeNameResolver,
    ) {}

    /**
     * @param  array<Node>  $nodes  every node of the body of `$scope`
     */
    public function analyze(ClassMethod|Function_|Closure $scope, array $nodes): VariableUsage
    {
        $nesting = new LoopAndClosureNestingNodeVisitor;
        (new NodeTraverser($nesting))->traverse($scope->stmts ?? []);

        $occurrences = $this->occurrencesByName($nodes);
        $resultConsumedCallIds = $this->resultConsumedCallIds($nodes);

        return new VariableUsage(
            occurrences: $occurrences,
            readOccurrences: $this->withoutReloadReceivers($occurrences, $this->reloadReceiverIds($nodes)),
            assignedNames: $this->assignedNames($nodes),
            consumedOnTheSpotIds: $this->consumedOnTheSpotIds($nodes),
            aliasedReceiverIds: $this->aliasedReceiverIds($nodes, $resultConsumedCallIds),
            resultConsumedCallIds: $resultConsumedCallIds,
            locallyCreatedNames: $this->locallyCreatedNames($scope, $nodes),
            nodeIdsInsideClosures: $nesting->getNodeIdsInsideClosures(),
            nodeIdsInsideLoops: $nesting->getNodeIdsInsideLoops(),
        );
    }

    /**
     * @param  array<Node>  $nodes
     * @return array<string, list<Variable>>
     */
    private function occurrencesByName(array $nodes): array
    {
        $occurrences = [];
        foreach ($nodes as $node) {
            if ($node instanceof Variable && is_string($node->name)) {
                $occurrences[$node->name][] = $node;
            }
        }

        return $occurrences;
    }

    /**
     * Reloading a variable again never observes the state the previous reload replaced, so the
     * receivers of `fresh()` / `refresh()` are not reads.
     *
     * @param  array<string, list<Variable>>  $occurrences
     * @param  array<int, true>  $reloadReceiverIds
     * @return array<string, list<Variable>>
     */
    private function withoutReloadReceivers(array $occurrences, array $reloadReceiverIds): array
    {
        $reads = [];
        foreach ($occurrences as $name => $variables) {
            foreach ($variables as $variable) {
                if (! isset($reloadReceiverIds[spl_object_id($variable)])) {
                    $reads[$name][] = $variable;
                }
            }
        }

        return $reads;
    }

    /**
     * @param  array<Node>  $nodes
     * @return array<int, string>
     */
    private function assignedNames(array $nodes): array
    {
        $names = [];
        foreach ($nodes as $node) {
            if ($node instanceof Assign && $node->var instanceof Variable && is_string($node->var->name)) {
                $names[spl_object_id($node->expr)] = $node->var->name;
            }
        }

        return $names;
    }

    /**
     * @param  array<Node>  $nodes
     * @return array<int, true>
     */
    private function consumedOnTheSpotIds(array $nodes): array
    {
        $ids = [];
        foreach ($nodes as $node) {
            if ($node instanceof Assign && $node->var instanceof Variable) {
                $ids[spl_object_id($node->var)] = true;
            }

            if ($this->isDereferencedOnTheSpot($node)) {
                $ids[spl_object_id($node->var)] = true;
            }
        }

        return $ids;
    }

    /**
     * An access that turns the object into something else on the spot: a property value, a method
     * result, an array entry. What flows on is that result, never the reloaded receiver.
     *
     * @phpstan-assert-if-true PropertyFetch|NullsafePropertyFetch|MethodCall|NullsafeMethodCall|ArrayDimFetch $node
     */
    private function isDereferencedOnTheSpot(Node $node): bool
    {
        return ($node instanceof PropertyFetch
            || $node instanceof NullsafePropertyFetch
            || $node instanceof MethodCall
            || $node instanceof NullsafeMethodCall
            || $node instanceof ArrayDimFetch)
            && $node->var instanceof Variable;
    }

    /**
     * A method call whose result is used as a value may hand its receiver back (`fill()`, `load()`
     * return `$this`): passed as an argument, stored, assigned or returned, the receiver has an
     * alias. A call whose result is discarded or dereferenced on the spot has none.
     *
     * @param  array<Node>  $nodes
     * @param  array<int, true>  $consumed  the method calls whose result is discarded or read on the spot
     * @return array<int, true>
     */
    private function aliasedReceiverIds(array $nodes, array $consumed): array
    {
        $chained = $this->chainedCallIds($nodes);

        $ids = [];
        foreach ($nodes as $node) {
            if (! $node instanceof MethodCall && ! $node instanceof NullsafeMethodCall) {
                continue;
            }

            $id = spl_object_id($node);
            if (isset($consumed[$id]) || isset($chained[$id]) || $this->nodeNameResolver->isName($node->name, 'fresh')) {
                continue;
            }

            $root = $this->fluentCallRoot($node);
            if ($root instanceof Variable) {
                $ids[spl_object_id($root)] = true;
            }
        }

        return $ids;
    }

    /**
     * The method calls whose result is thrown away or read on the spot (`$model->fresh()->status`):
     * what flows on is a separate value, never the object the call returned.
     *
     * @param  array<Node>  $nodes
     * @return array<int, true>
     */
    private function resultConsumedCallIds(array $nodes): array
    {
        $ids = [];
        foreach ($nodes as $node) {
            if ($node instanceof Expression && $this->isMethodCall($node->expr)) {
                $ids[spl_object_id($node->expr)] = true;
            }

            if (($node instanceof PropertyFetch || $node instanceof NullsafePropertyFetch || $node instanceof ArrayDimFetch)
                && $this->isMethodCall($node->var)) {
                $ids[spl_object_id($node->var)] = true;
            }
        }

        return $ids;
    }

    /**
     * The method calls that are the receiver of a further method call: only the last link of a
     * fluent chain decides what escapes.
     *
     * @param  array<Node>  $nodes
     * @return array<int, true>
     */
    private function chainedCallIds(array $nodes): array
    {
        $ids = [];
        foreach ($nodes as $node) {
            if (($node instanceof MethodCall || $node instanceof NullsafeMethodCall) && $this->isMethodCall($node->var)) {
                $ids[spl_object_id($node->var)] = true;
            }
        }

        return $ids;
    }

    private function isMethodCall(Node $node): bool
    {
        return $node instanceof MethodCall || $node instanceof NullsafeMethodCall;
    }

    /**
     * @param  array<Node>  $nodes
     * @return array<int, true>
     */
    private function reloadReceiverIds(array $nodes): array
    {
        $ids = [];
        foreach ($nodes as $node) {
            if ($node instanceof MethodCall
                && $node->var instanceof Variable
                && $this->nodeNameResolver->isNames($node->name, ['fresh', 'refresh'])) {
                $ids[spl_object_id($node->var)] = true;
            }
        }

        return $ids;
    }

    /**
     * Variables that only ever hold an instance built right here: never a parameter or a closure
     * import, and never assigned anything but a `new` or a static call on a model.
     *
     * @param  array<Node>  $nodes
     * @return array<string, true>
     */
    private function locallyCreatedNames(ClassMethod|Function_|Closure $scope, array $nodes): array
    {
        $onlyBuiltHere = [];
        foreach ($nodes as $node) {
            if ($node instanceof Assign && $node->var instanceof Variable && is_string($node->var->name)) {
                $name = $node->var->name;
                $onlyBuiltHere[$name] = ($onlyBuiltHere[$name] ?? true) && $this->buildsNewInstance($node->expr);
            }
        }

        $sharedNames = $this->sharedNames($scope);

        $names = [];
        foreach ($onlyBuiltHere as $name => $isOnlyBuiltHere) {
            if ($isOnlyBuiltHere && ! isset($sharedNames[$name])) {
                $names[$name] = true;
            }
        }

        return $names;
    }

    /**
     * The variables a scope receives from its caller: its parameters and what a closure imports.
     * Every other way a variable gets bound outside a plain assignment (`foreach`, `global`,
     * `static`, `&`, `list()`) leaves an occurrence that is not an assignment target, which
     * already blocks the rewrite when it comes before the call.
     *
     * @return array<string, true>
     */
    private function sharedNames(ClassMethod|Function_|Closure $scope): array
    {
        $variables = [];
        foreach ($scope->params as $param) {
            $variables[] = $param->var;
        }

        if ($scope instanceof Closure) {
            foreach ($scope->uses as $use) {
                $variables[] = $use->var;
            }
        }

        $names = [];
        foreach ($variables as $variable) {
            if ($variable instanceof Variable && is_string($variable->name)) {
                $names[$variable->name] = true;
            }
        }

        return $names;
    }

    /**
     * A call to one of the Eloquent constructors on a model class (`Job::find($id)`,
     * `Job::factory()->create()`, `Job::query()->first()`) hands over a persisted instance nobody
     * else holds. Any other source may hand back a shared one: a custom static getter
     * (`Tenant::current()`), a helper (`auth()->user()`, `app(Job::class)`), a call on a variable,
     * a property getter.
     */
    private function buildsNewInstance(Expr $expr): bool
    {
        $root = $this->fluentCallRoot($expr);

        if (! $root instanceof StaticCall) {
            return false;
        }

        $className = $this->nodeNameResolver->getName($root->class);
        if ($className === null || ! (new ObjectType(Model::class))->isSuperTypeOf(new ObjectType($className))->yes()) {
            return false;
        }

        Assert::isInstanceOfAny($expr, [StaticCall::class, MethodCall::class, NullsafeMethodCall::class]);

        return in_array(strtolower((string) $this->nodeNameResolver->getName($expr->name)), self::MODEL_CONSTRUCTORS, true);
    }

    private function fluentCallRoot(Expr $expr): Expr
    {
        while ($expr instanceof MethodCall || $expr instanceof NullsafeMethodCall) {
            $expr = $expr->var;
        }

        return $expr;
    }
}
