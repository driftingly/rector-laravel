<?php

declare(strict_types=1);

namespace RectorLaravel\Rector\Class_;

use PhpParser\Node;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\New_;
use PhpParser\Node\Name\FullyQualified;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Function_;
use Rector\PhpParser\Node\BetterNodeFinder;
use RectorLaravel\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\ConfiguredCodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

class AddJsonResponseRector extends AbstractRector
{
    public function __construct(
        private readonly BetterNodeFinder $betterNodeFinder,
    ) {}

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(
            'Add JsonResponse as return type to methods that return a JSON response',
            [
                new ConfiguredCodeSample(
                    <<<'CODE_SAMPLE'
public function __invoke()
{
    return new JsonResponse(['key' => 'value']);
}

public function __invoke()
{
    return response()->json(['key' => 'value']);
}
CODE_SAMPLE
                    ,
                    <<<'CODE_SAMPLE'
public function __invoke(): JsonResponse
{
    return new JsonResponse(['key' => 'value']);
}

public function __invoke(): JsonResponse
{
    return response()->json(['key' => 'value']);
}
CODE_SAMPLE
                    ,
                    [
                        //self::EXCLUDE_METHODS => ['present'],
                    ]
                ),
            ]
        );
    }

    /**
     * @return array<class-string<Node>>
     */
    public function getNodeTypes(): array
    {
        return [ClassMethod::class, Function_::class, MethodCall::class];
    }

    /**
     * @param  MethodCall  $node
     */
    public function refactor(Node $node): ?Node
    {
        if ($node instanceof MethodCall) {
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
        } else {
            $returns = $this->betterNodeFinder->findReturnsScoped($node);
            if (\count($returns) !== 1) {
                return null;
            }

            $return = $returns[0];

            if (!$return->expr instanceof New_) {
                return null;
            }

            if (!$return->expr->class instanceof FullyQualified) {
                return null;
            }

            if ((string) $return->expr->class !== 'Illuminate\Http\JsonResponse') {
                return null;
            }
        }

        $node->returnType = new FullyQualified('Illuminate\Http\JsonResponse');
        return $node;
    }
}
