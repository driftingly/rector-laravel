<?php

declare(strict_types=1);

namespace RectorLaravel\Rector\FuncCall;

use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr\ErrorSuppress;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Name\FullyQualified;
use PhpParser\Node\VariadicPlaceholder;
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
        return [ErrorSuppress::class, FuncCall::class];
    }

    /**
     * @param  ErrorSuppress|FuncCall  $node
     */
    public function refactor(Node $node): ?StaticCall
    {
        // File::delete() suppresses the failure itself, so the suppressor is dropped
        $funcCall = $node instanceof ErrorSuppress ? $node->expr : $node;

        if (! $funcCall instanceof FuncCall) {
            return null;
        }

        if (! $this->isName($funcCall->name, 'unlink')) {
            return null;
        }

        if ($funcCall->isFirstClassCallable()) {
            return $this->createFileDeleteCall([new VariadicPlaceholder]);
        }

        // the stream context argument has no equivalent on the facade
        if (count($funcCall->args) !== 1) {
            return null;
        }

        $arg = $funcCall->getArg('filename', 0);

        if (! $arg instanceof Arg) {
            return null;
        }

        // the facade parameter is named $paths, so a filename: argument is passed positionally
        return $this->createFileDeleteCall([new Arg($arg->value)]);
    }

    /**
     * @param  array<Arg|VariadicPlaceholder>  $args
     */
    private function createFileDeleteCall(array $args): StaticCall
    {
        return new StaticCall(new FullyQualified('Illuminate\Support\Facades\File'), 'delete', $args);
    }
}
