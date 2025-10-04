<?php

declare(strict_types=1);

namespace RectorLaravel\Rector\MethodCall;

use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\PropertyFetch;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Identifier;
use PhpParser\Node\Scalar\String_;
use RectorLaravel\AbstractRector;
use RectorLaravel\NodeAnalyzer\QueryBuilderAnalyzer;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \RectorLaravel\Tests\Rector\MethodCall\EloquentWhereIdToWhereKeyRector\EloquentWhereIdToWhereKeyRectorTest
 */
final class EloquentWhereIdToWhereKeyRector extends AbstractRector
{
    public function __construct(
        private readonly QueryBuilderAnalyzer $queryBuilderAnalyzer
    ) {}

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(
            'Refactor model calls to the primary key using the `whereKey` and `whereKeyNot` methods',
            [
                new CodeSample(
                    <<<'CODE_SAMPLE'
User::where('id', '=', $user->id)->get();
User::where('id', $user->id)->get();
CODE_SAMPLE,
                    <<<'CODE_SAMPLE'
User::whereKey($user)->get();
User::whereKey($user)->get();
CODE_SAMPLE
                ),
                new CodeSample(
                    <<<'CODE_SAMPLE'
User::where('id', '!=', $user->id)->get();
User::whereNot('id', $user->id)->get();
CODE_SAMPLE,
                    <<<'CODE_SAMPLE'
User::whereKeyNot($user)->get();
User::whereKeyNot($user)->get();
CODE_SAMPLE
                ),
            ]
        );
    }

    /**
     * @return array<class-string<Node>>
     */
    public function getNodeTypes(): array
    {
        return [MethodCall::class, StaticCall::class];
    }

    /**
     * @param  MethodCall|StaticCall  $node
     */
    public function refactor(Node $node): ?Node
    {
        if (! $node instanceof MethodCall && ! $node instanceof StaticCall) {
            return null;
        }

        $isWhere = $this->queryBuilderAnalyzer->isMatchingCall($node, 'where');
        $isWhereNot = $this->queryBuilderAnalyzer->isMatchingCall($node, 'whereNot');

        if (! $isWhere && ! $isWhereNot) {
            return null;
        }

        $args = $node->getArgs();
        $argCount = count($args);

        if ($argCount === 2) {
            return $this->refactorTwoArgumentWhere($node, $args, $isWhereNot);
        }

        if ($argCount === 3 && $isWhere) {
            return $this->refactorThreeArgumentWhere($node, $args);
        }

        return null;
    }

    /**
     * @param  Arg[]  $args
     */
    private function refactorTwoArgumentWhere(MethodCall|StaticCall $node, array $args, bool $isWhereNot): ?Node
    {
        if (! $args[0] instanceof Arg || ! $args[0]->value instanceof String_) {
            return null;
        }

        $columnName = $args[0]->value->value;
        if ($columnName !== 'id') {
            return null;
        }

        if (! $args[1] instanceof Arg || ! $args[1]->value instanceof PropertyFetch) {
            return null;
        }

        $propertyFetch = $args[1]->value;
        if (! $this->isName($propertyFetch->name, 'id')) {
            return null;
        }

        // where() with 2 args uses '=' operator -> whereKey
        // whereNot() with 2 args uses '!=' operator -> whereKeyNot
        $newMethodName = $isWhereNot ? 'whereKeyNot' : 'whereKey';
        $node->name = new Identifier($newMethodName);
        $node->args = [new Arg($propertyFetch->var)];

        return $node;
    }

    /**
     * @param  Arg[]  $args
     */
    private function refactorThreeArgumentWhere(MethodCall|StaticCall $node, array $args): ?Node
    {
        if (! $args[0] instanceof Arg || ! $args[0]->value instanceof String_) {
            return null;
        }

        $columnName = $args[0]->value->value;
        if ($columnName !== 'id') {
            return null;
        }

        if (! $args[1] instanceof Arg || ! $args[1]->value instanceof String_) {
            return null;
        }

        $operator = $args[1]->value->value;
        if (! in_array($operator, ['=', '!='], true)) {
            return null;
        }

        if (! $args[2] instanceof Arg || ! $args[2]->value instanceof PropertyFetch) {
            return null;
        }

        $propertyFetch = $args[2]->value;
        if (! $this->isName($propertyFetch->name, 'id')) {
            return null;
        }

        $newMethodName = $operator === '=' ? 'whereKey' : 'whereKeyNot';
        $node->name = new Identifier($newMethodName);
        $node->args = [new Arg($propertyFetch->var)];

        return $node;
    }
}
