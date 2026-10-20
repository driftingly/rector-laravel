<?php

declare(strict_types=1);

namespace RectorLaravel\Rector\MethodCall;

use PhpParser\Node;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Identifier;
use PHPStan\Type\ObjectType;
use RectorLaravel\AbstractRector;
use RectorLaravel\Tests\Rector\MethodCall\CollectionWhereToContainsRector\CollectionWhereToContainsRectorTest;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see CollectionWhereToContainsRectorTest
 */
final class CollectionWhereToContainsRector extends AbstractRector
{
    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(
            'Convert `where()->isNotEmpty()` to `contains()` and `where()->isEmpty()` to `doesntContain()` on collections',
            [
                new CodeSample(
                    <<<'CODE_SAMPLE'
use Illuminate\Support\Collection;

$collection = new Collection([['status' => 'open']]);
$collection->where('status', 'open')->isNotEmpty();
$collection->where('priority', '>', 3)->isEmpty();
CODE_SAMPLE
                    ,
                    <<<'CODE_SAMPLE'
use Illuminate\Support\Collection;

$collection = new Collection([['status' => 'open']]);
$collection->contains('status', 'open');
$collection->doesntContain('priority', '>', 3);
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
        return [MethodCall::class];
    }

    /**
     * @param  MethodCall  $node
     */
    public function refactor(Node $node): ?MethodCall
    {
        if ($this->isName($node->name, 'isNotEmpty')) {
            $replacement = 'contains';
        } elseif ($this->isName($node->name, 'isEmpty')) {
            $replacement = 'doesntContain';
        } else {
            return null;
        }

        $whereCall = $node->var;
        if (! $whereCall instanceof MethodCall || ! $this->isName($whereCall->name, 'where')) {
            return null;
        }

        // 1-arg where() tests truthiness, while 1-arg contains() is an in_array() check
        if ($whereCall->isFirstClassCallable() || ! in_array(count($whereCall->getArgs()), [2, 3], true)) {
            return null;
        }

        foreach ($whereCall->getArgs() as $arg) {
            if ($arg->unpack) {
                return null;
            }
        }

        if (! $this->isObjectType($whereCall->var, new ObjectType('Illuminate\Support\Enumerable'))) {
            return null;
        }

        $whereCall->name = new Identifier($replacement);

        return $whereCall;
    }
}
