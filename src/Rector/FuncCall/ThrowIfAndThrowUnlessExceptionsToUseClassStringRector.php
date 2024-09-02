<?php

namespace RectorLaravel\Rector\FuncCall;

use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr\ClassConstFetch;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Expr\New_;
use PhpParser\Node\Name;
use Rector\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \RectorLaravel\Tests\Rector\FuncCall\ThrowIfAndThrowUnlessExceptionsToUseClassStringRector\ThrowIfAndThrowUnlessExceptionsToUseClassStringRectorTest
 */
class ThrowIfAndThrowUnlessExceptionsToUseClassStringRector extends AbstractRector
{
    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('changes use of a new throw instance to class string', [
            new CodeSample(<<<'CODE_SAMPLE'
throw_if($condition, new MyException('custom message'));
CODE_SAMPLE
, <<<'CODE_SAMPLE'
throw_if($condition, MyException::class, 'custom message');
CODE_SAMPLE
),
        ]);
    }

    public function getNodeTypes(): array
    {
        return [FuncCall::class];
    }

    /**
     * @param  FuncCall  $node
     */
    public function refactor(Node $node): ?FuncCall
    {
        if (! $this->isNames($node, ['throw_if', 'throw_unless'])) {
            return null;
        }

        if (count($node->args) !== 2 || ! $node->args[1] instanceof Arg) {
            return null;
        }

        $exception = $node->args[1]->value;
        if (! $exception instanceof New_) {
            return null;
        }

        $class = $exception->class;
        if (! $class instanceof Name) {
            return null;
        }

        // convert the class to a class string
        $node->args[1] = new Arg(new ClassConstFetch($class, 'class'));
        $node->args = array_merge(is_array($node->args) ? $node->args : iterator_to_array($node->args), $exception->getArgs());

        return $node;
    }
}
