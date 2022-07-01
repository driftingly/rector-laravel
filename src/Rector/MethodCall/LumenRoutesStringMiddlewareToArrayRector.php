<?php

declare(strict_types=1);

namespace Rector\Laravel\Rector\MethodCall;

use PhpParser\Node;
use PhpParser\Node\Expr\Array_;
use PhpParser\Node\Expr\ArrayItem;
use PhpParser\Node\Expr\MethodCall;
use Rector\Core\NodeManipulator\ArrayManipulator;
use Rector\Core\Rector\AbstractRector;
use Rector\Laravel\NodeAnalyzer\LumenRouteRegisteringMethodAnalyzer;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \Rector\Laravel\Tests\Rector\MethodCall\RoutesStringMiddlewareToArrayRector\RoutesStringMiddlewareToArrayRectorTest
 */
final class LumenRoutesStringMiddlewareToArrayRector extends AbstractRector
{
    public function __construct(
        private ArrayManipulator $arrayManipulator,
        private LumenRouteRegisteringMethodAnalyzer $lumenRouteRegisteringMethodAnalyzer
    ) {
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(
            'Changes middlewares from rule definitions from string to array notation.',
            [new CodeSample(<<<'CODE_SAMPLE'
$router->get('/user', ['middleware => 'test']);
$router->post('/user', ['middleware => 'test|authentication']);
CODE_SAMPLE
            , <<<'CODE_SAMPLE'
$router->get('/user', ['middleware => ['test']]);
$router->post('/user', ['middleware => ['test', 'authentication']]);
CODE_SAMPLE)]
        );
    }

    /**
     * @return array<class-string<Node>>
     */
    public function getNodeTypes(): array
    {
        return [MethodCall::class];
    }

    public function refactor(Node $node): ?Node
    {
        if (! $node instanceof MethodCall) {
            return null;
        }

        if (! $this->lumenRouteRegisteringMethodAnalyzer->isLumenRoutingClass($node)) {
            return null;
        }

        $attributes = $this->getRouterMethodAttributes($node);
        if ($attributes === null) {
            return null;
        }

        $middleware = $this->findItemInArrayByKey($attributes, 'middleware');
        if ($middleware === null) {
            return null;
        }

        /** @var Node\Scalar\String_|Node\Expr\Array_ $middlewareValue */
        $middlewareValue = $middleware->value;
        if ($middlewareValue instanceof Node\Expr\Array_) {
            return null;
        }

        /** @var string $middlewareString */
        $middlewareString = $middlewareValue->value;
        $splitMiddleware = explode('|', $middlewareString);

        $newMiddlewareArray = new Node\Expr\Array_([]);
        foreach ($splitMiddleware as $item) {
            $newMiddlewareArray->items[] = new ArrayItem(new Node\Scalar\String_($item));
        }

        $this->replaceItemInArrayByKey(
            $attributes,
            new ArrayItem($newMiddlewareArray, new Node\Scalar\String_('middleware')),
            'middleware'
        );

        return $node;
    }

    private function getRouterMethodAttributes(MethodCall $node): ?Node\Expr\Array_
    {
        $attributes = null;
        if ($this->lumenRouteRegisteringMethodAnalyzer->isRoutesRegisterGroup($node->name)) {
            $attributes = $node->getArgs()[0]
                ->value;
        }

        if ($this->lumenRouteRegisteringMethodAnalyzer->isRoutesRegisterRoute($node->name)) {
            $attributes = $node->getArgs()[1]
                ->value;
        }

        if (! $attributes instanceof Node\Expr\Array_) {
            return null;
        }

        return $attributes;
    }

    private function findItemInArrayByKey(Array_ $array, string $keyName): ?ArrayItem
    {
        foreach ($array->items as $i => $item) {
            if ($item === null) {
                continue;
            }
            if (! $this->arrayManipulator->hasKeyName($item, $keyName)) {
                continue;
            }
            $foundArrayItem = $array->items[$i];
            if (! $foundArrayItem instanceof ArrayItem) {
                continue;
            }
            return $item;
        }
        return null;
    }

    private function replaceItemInArrayByKey(Array_ $array, ArrayItem $newItem, string $keyName): void
    {
        foreach ($array->items as $i => $item) {
            if ($item === null) {
                continue;
            }
            if (! $this->arrayManipulator->hasKeyName($item, $keyName)) {
                continue;
            }
            if (! $array->items[$i] instanceof ArrayItem) {
                continue;
            }

            $array->items[$i] = $newItem;
        }
    }
}
