<?php

declare(strict_types=1);

namespace RectorLaravel\Rector\ClassMethod;

use Illuminate\Database\Eloquent\Model;
use PhpParser\Node;
use PhpParser\Node\Expr\Closure;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Identifier;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Function_;
use PHPStan\Type\ObjectType;
use Rector\PhpParser\Node\BetterNodeFinder;
use Rector\RuleDoc\CodeSample\CodeSample;
use Rector\RuleDoc\RuleDefinition;
use RectorLaravel\AbstractRector;
use RectorLaravel\NodeAnalyzer\NullTestAnalyzer;
use RectorLaravel\NodeAnalyzer\VariableUsageAnalyzer;
use RectorLaravel\Tests\Rector\ClassMethod\ModelFreshToRefreshRector\ModelFreshToRefreshRectorTest;
use RectorLaravel\ValueObject\NullTestedExpressions;
use RectorLaravel\ValueObject\VariableUsage;

use function is_string;

/**
 * `fresh()` is typed `static|null`, `refresh()` is typed `$this`, and the two are not
 * interchangeable:
 *
 * - `refresh()` reloads the receiver in place. The rewrite only happens when the receiver is a
 *   local variable loaded or created in the same scope (`Model::create()`, `Model::find()`...) and
 *   never read afterwards: see {@see VariableUsage}. A property, a parameter, a helper result or
 *   a loop variable is left alone.
 * - When the row is gone, `refresh()` throws instead of returning null. The rewrite is skipped
 *   when the result is tested for null: see {@see NullTestedExpressions}.
 *
 * @see ModelFreshToRefreshRectorTest
 */
final class ModelFreshToRefreshRector extends AbstractRector
{
    public function __construct(
        private readonly BetterNodeFinder $betterNodeFinder,
        private readonly NullTestAnalyzer $nullTestAnalyzer,
        private readonly VariableUsageAnalyzer $variableUsageAnalyzer,
    ) {}

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(
            'Convert Model::fresh() to the non-nullable Model::refresh() when the receiver is a local variable created in the same scope (create(), find()...) and never read again',
            [
                new CodeSample(
                    <<<'CODE_SAMPLE'
$job = Job::create(['status' => 'pending']);

$this->assertSame('done', $job->fresh()->status);
CODE_SAMPLE,
                    <<<'CODE_SAMPLE'
$job = Job::create(['status' => 'pending']);

$this->assertSame('done', $job->refresh()->status);
CODE_SAMPLE
                ),
            ],
        );
    }

    /**
     * @return array<class-string<Node>>
     */
    public function getNodeTypes(): array
    {
        return [ClassMethod::class, Function_::class, Closure::class];
    }

    /**
     * @param  ClassMethod|Function_|Closure  $node
     */
    public function refactor(Node $node): ?Node
    {
        if ($node->stmts === null) {
            return null;
        }

        $freshCalls = $this->findFreshCallsOnModelVariables($node->stmts);
        if ($freshCalls === []) {
            return null;
        }

        $nodes = $this->betterNodeFinder->find($node->stmts, static fn (): bool => true);

        $usage = $this->variableUsageAnalyzer->analyze($node, $nodes);
        $nullTests = $this->nullTestAnalyzer->analyze($nodes);

        $hasChanged = false;
        foreach ($freshCalls as $freshCall) {
            if (! $this->canBeReplacedByRefresh($freshCall, $usage, $nullTests)) {
                continue;
            }

            $freshCall->name = new Identifier('refresh');
            $hasChanged = true;
        }

        return $hasChanged ? $node : null;
    }

    /**
     * A no-argument `fresh()` on a plain `$variable` typed as an Eloquent model. A property, an
     * array entry, an `Eloquent\Collection` (it has `fresh()` but no `refresh()`) and an
     * eager-loading argument `refresh()` cannot carry are out of reach.
     *
     * Looked up first and alone, so that a body without any such call costs one traversal and
     * no analysis.
     *
     * @param  array<Node>  $stmts
     * @return list<MethodCall>
     */
    private function findFreshCallsOnModelVariables(array $stmts): array
    {
        $freshCalls = [];
        foreach ($this->betterNodeFinder->findInstanceOf($stmts, MethodCall::class) as $methodCall) {
            if ($this->isFreshCallOnModelVariable($methodCall)) {
                $freshCalls[] = $methodCall;
            }
        }

        return $freshCalls;
    }

    private function isFreshCallOnModelVariable(MethodCall $methodCall): bool
    {
        return $methodCall->name instanceof Identifier
            && $this->isName($methodCall->name, 'fresh')
            && $methodCall->args === []
            && $methodCall->var instanceof Variable
            && is_string($methodCall->var->name)
            && $this->isObjectType($methodCall->var, new ObjectType(Model::class));
    }

    private function canBeReplacedByRefresh(MethodCall $freshCall, VariableUsage $usage, NullTestedExpressions $nullTests): bool
    {
        $receiver = $freshCall->var;

        return $receiver instanceof Variable
            && $usage->isLocallyCreated($receiver)
            && ! $usage->isObservedAfterwards($freshCall)
            && ! $this->resultIsNullTested($freshCall, $usage, $nullTests);
    }

    /**
     * True when the reloaded value, directly or through the variable it is assigned to, is tested
     * for null: `refresh()` can no longer produce null.
     */
    private function resultIsNullTested(MethodCall $freshCall, VariableUsage $usage, NullTestedExpressions $nullTests): bool
    {
        if ($nullTests->contains($freshCall)) {
            return true;
        }

        $assignedName = $usage->assignedVariableName($freshCall);
        if ($assignedName === null) {
            return false;
        }

        foreach ($usage->occurrencesOf($assignedName) as $occurrence) {
            if ($nullTests->contains($occurrence)) {
                return true;
            }
        }

        return false;
    }
}
