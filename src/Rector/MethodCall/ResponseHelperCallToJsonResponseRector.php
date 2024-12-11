<?php

declare(strict_types=1);

namespace RectorLaravel\Rector\MethodCall;

use PhpParser\Node;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\New_;
use PhpParser\Node\Name\FullyQualified;
use RectorLaravel\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \RectorLaravel\Tests\Rector\MethodCall\ResponseHelperCallToJsonResponseRector\ResponseHelperCallToJsonResponseRectorTest
 */
final class ResponseHelperCallToJsonResponseRector extends AbstractRector
{
    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Use new JsonResponse instead of response()->json()', [
            new CodeSample(
                <<<'CODE_SAMPLE'
response()->json(['key' => 'value']);
CODE_SAMPLE

                ,
                <<<'CODE_SAMPLE'
return new JsonResponse(['key' => 'value']);
CODE_SAMPLE
            ),
        ]);
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
    public function refactor(Node $node): ?Node
    {
        if (! $this->isName($node->name, 'json')) {
            return null;
        }

        if (! $this->isName($node->var, 'response')) {
            return null;
        }

        if (! $node->var instanceof FuncCall) {
            return null;
        }

        if ($node->var->args !== []) {
            return null;
        }

        return new New_(new FullyQualified('Illuminate\Http\JsonResponse'), $node->args);
    }
}
