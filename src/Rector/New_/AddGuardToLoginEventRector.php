<?php

declare(strict_types=1);

namespace RectorLaravel\Rector\New_;

use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr\Assign;
use PhpParser\Node\Expr\New_;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Scalar\String_;
use PhpParser\Node\Stmt\Expression;
use Rector\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @changelog https://github.com/laravel/framework/commit/f5d8c0a673aa9fc6cd94aa4858a0027fe550a22e#diff-162a49c054acde9f386ec735607b95bc4a1c0c765a6f46da8de9a8a4ef5199d3
 * @changelog https://github.com/laravel/framework/pull/25261
 *
 * @see \RectorLaravel\Tests\Rector\New_\AddGuardToLoginEventRector\AddGuardToLoginEventRectorTest
 */
final class AddGuardToLoginEventRector extends AbstractRector
{
    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(
            'Add new $guard argument to Illuminate\Auth\Events\Login',
            [
                new CodeSample(
                    <<<'CODE_SAMPLE'
use Illuminate\Auth\Events\Login;

final class SomeClass
{
    public function run(): void
    {
        $loginEvent = new Login('user', false);
    }
}
CODE_SAMPLE
                    ,
                    <<<'CODE_SAMPLE'
use Illuminate\Auth\Events\Login;

final class SomeClass
{
    public function run(): void
    {
        $guard = config('auth.defaults.guard');
        $loginEvent = new Login($guard, 'user', false);
    }
}
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
        return [Expression::class];
    }

    /**
     * @param  Expression  $node
     * @return \PhpParser\Node|mixed[]|int|null
     */
    public function refactor(Node $node)
    {
        $newNode = $this->getNewNode($node);

        if (! $newNode instanceof New_) {
            return null;
        }

        if (! $this->isName($newNode->class, 'Illuminate\Auth\Events\Login')) {
            return null;
        }

        if (count($newNode->args) === 3) {
            return null;
        }

        $guardVariable = new Variable('guard');
        $assign = $this->createGuardAssign($guardVariable);

        $newNode->args = array_merge([new Arg($guardVariable)], $newNode->args);

        return [new Expression($assign), $node];
    }

    private function createGuardAssign(Variable $guardVariable): Assign
    {
        $string = new String_('auth.defaults.guard');

        return new Assign($guardVariable, $this->nodeFactory->createFuncCall('config', [$string]));
    }

    private function getNewNode(Expression $expression): ?New_
    {
        if ($expression->expr instanceof Assign && $expression->expr->expr instanceof New_) {
            return $expression->expr->expr;
        }

        if ($expression->expr instanceof New_) {
            return $expression->expr;
        }

        return null;
    }
}
