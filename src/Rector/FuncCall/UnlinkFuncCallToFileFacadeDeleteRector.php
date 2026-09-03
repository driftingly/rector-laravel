<?php

declare(strict_types=1);

namespace RectorLaravel\Rector\FuncCall;

use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Identifier;
use RectorLaravel\AbstractRector;
use RectorLaravel\Tests\Rector\FuncCall\UnlinkFuncCallToFileFacadeDeleteRector\UnlinkFuncCallToFileFacadeDeleteRectorTest;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @changelog https://laravel.com/docs/filesystem
 *
 * @see UnlinkFuncCallToFileFacadeDeleteRectorTest
 */
final class UnlinkFuncCallToFileFacadeDeleteRector extends AbstractRector
{
    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(
            'Use the File facade instead of the unlink() function.',
            [
                new CodeSample(
                    <<<'CODE_SAMPLE'
unlink($path);
CODE_SAMPLE
                    ,
                    <<<'CODE_SAMPLE'
\Illuminate\Support\Facades\File::delete($path);
CODE_SAMPLE
                    ,
                ),
            ]
        );
    }

    /**
     * @return array<class-string<Node>>
     */
    public function getNodeTypes(): array
    {
        return [FuncCall::class];
    }

    /**
     * @param  FuncCall  $node
     */
    public function refactor(Node $node): ?StaticCall
    {
        if (! $this->isName($node->name, 'unlink')) {
            return null;
        }

        // the stream context argument has no equivalent on the facade
        if (count($node->args) !== 1) {
            return null;
        }

        $arg = $node->args[0];

        // skips first class callables and unpacked arguments
        if (! $arg instanceof Arg || $arg->unpack) {
            return null;
        }

        if ($arg->name instanceof Identifier && ! $this->isName($arg->name, 'filename')) {
            return null;
        }

        return $this->nodeFactory->createStaticCall(
            'Illuminate\Support\Facades\File',
            'delete',
            [$arg->value],
        );
    }
}
