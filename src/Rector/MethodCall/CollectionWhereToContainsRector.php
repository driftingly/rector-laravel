<?php

declare(strict_types=1);

namespace RectorLaravel\Rector\MethodCall;

use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Identifier;
use PhpParser\Node\Scalar\String_;
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
    /**
     * 1-arg where() tests truthiness, while 1-arg contains() is an in_array() check.
     *
     * @var array<string, list<int>>
     */
    private const array ALLOWED_ARGUMENT_COUNTS_BY_WHERE_METHOD = [
        'where' => [2, 3],
        'whereStrict' => [2],
    ];

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(
            'Convert `where()`/`whereStrict()` followed by `isNotEmpty()` or `isEmpty()` to `contains()` or `doesntContain()` on collections',
            [
                new CodeSample(
                    <<<'CODE_SAMPLE'
use Illuminate\Support\Collection;

$collection = new Collection([['status' => 'open']]);
$collection->where('status', 'open')->isNotEmpty();
$collection->where('priority', '>', 3)->isEmpty();
$collection->whereStrict('status', 'open')->isNotEmpty();
CODE_SAMPLE
                    ,
                    <<<'CODE_SAMPLE'
use Illuminate\Support\Collection;

$collection = new Collection([['status' => 'open']]);
$collection->contains('status', 'open');
$collection->doesntContain('priority', '>', 3);
$collection->contains('status', '===', 'open');
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
        if (! $whereCall instanceof MethodCall || $whereCall->isFirstClassCallable()) {
            return null;
        }

        $whereMethod = $this->getName($whereCall->name) ?? '';
        $allowedArgumentCounts = self::ALLOWED_ARGUMENT_COUNTS_BY_WHERE_METHOD[$whereMethod] ?? [];
        if (! in_array(count($whereCall->getArgs()), $allowedArgumentCounts, true)) {
            return null;
        }

        foreach ($whereCall->getArgs() as $arg) {
            if ($arg->unpack || $arg->name instanceof Identifier) {
                return null;
            }
        }

        if (! $this->isObjectType($whereCall->var, new ObjectType('Illuminate\Support\Enumerable'))) {
            return null;
        }

        if ($whereMethod === 'whereStrict') {
            // containsStrict() skips the enum unwrapping that whereStrict() gets from operatorForWhere()
            array_splice($whereCall->args, 1, 0, [new Arg(new String_('==='))]);
        }

        $whereCall->name = new Identifier($replacement);

        return $whereCall;
    }
}
