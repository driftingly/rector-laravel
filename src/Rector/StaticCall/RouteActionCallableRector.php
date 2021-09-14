<?php

declare(strict_types=1);

namespace Rector\Laravel\Rector\StaticCall;

use PhpParser\Node;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Identifier;
use PHPStan\Reflection\Php\PhpMethodReflection;
use Rector\Core\Contract\Rector\ConfigurableRectorInterface;
use Rector\Core\Rector\AbstractRector;
use Rector\Core\Reflection\ReflectionResolver;
use Rector\Laravel\NodeFactory\RouterRegisterNodeAnalyzer;
use Rector\NodeTypeResolver\Node\AttributeKey;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\ConfiguredCodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;
use Symplify\SmartFileSystem\SmartFileInfo;

/**
 * @see https://laravel.com/docs/8.x/upgrade#automatic-controller-namespace-prefixing
 *
 * @see \Rector\Laravel\Tests\Rector\StaticCall\RouteActionCallableRector\RouteActionCallableRectorTest
 */
final class RouteActionCallableRector extends AbstractRector implements ConfigurableRectorInterface
{
    /**
     * @var string
     */
    public const ROUTES = 'routes';

    /**
     * @var string
     */
    public const NAMESPACE = 'namespace';

    private string $namespace = 'App\Http\Controllers';

    /**
     * @var array<string, string>
     */
    private array $routes = [];

    public function __construct(
        private ReflectionResolver $reflectionResolver,
        private RouterRegisterNodeAnalyzer $routerRegisterNodeAnalyzer
    ) {
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Use PHP callable syntax instead of string syntax for controller route declarations.', [
            new ConfiguredCodeSample(
                <<<'CODE_SAMPLE'
Route::get('/users', 'UserController@index');
CODE_SAMPLE

                ,
                <<<'CODE_SAMPLE'
Route::get('/users', [\App\Http\Controllers\UserController::class, 'index']);
CODE_SAMPLE
            ,
                [
                    self::NAMESPACE => 'App\Http\Controllers',
                ]
            ),
        ]);
    }

    /**
     * @return array<class-string<Node>>
     */
    public function getNodeTypes(): array
    {
        return [MethodCall::class, StaticCall::class];
    }

    /**
     * @param Node\Expr\MethodCall|StaticCall $node
     */
    public function refactor(Node $node): ?Node
    {
        if (! $this->routerRegisterNodeAnalyzer->isRegisterMethodStaticCall($node)) {
            return null;
        }

        $position = $this->getActionPosition($node->name);

        if (! isset($node->args[$position])) {
            return null;
        }

        $arg = $node->args[$position];

        $segments = $this->resolveControllerFromAction($this->valueResolver->getValue($arg->value));
        if ($segments === null) {
            return null;
        }

        $scope = $node->getAttribute(AttributeKey::SCOPE);

        $phpMethodReflection = $this->reflectionResolver->resolveMethodReflection($segments[0], $segments[1], $scope);

        if (! $phpMethodReflection instanceof PhpMethodReflection) {
            return null;
        }

        $node->args[$position]->value = $this->nodeFactory->createArray([
            $this->nodeFactory->createClassConstReference($segments[0]),
            $segments[1],
        ]);
        return $node;
    }

    public function configure(array $configuration): void
    {
        $this->routes = $configuration[self::ROUTES] ?? [];
        if (isset($configuration[self::NAMESPACE])) {
            $this->namespace = $configuration[self::NAMESPACE];
        }
    }

    /**
     * @param mixed $action
     * @return array<string>|null
     */
    private function resolveControllerFromAction($action): ?array
    {
        if (! $this->isActionString($action)) {
            return null;
        }

        $segments = explode('@', $action);
        if (count($segments) !== 2) {
            return null;
        }

        [$controller, $method] = $segments;
        $namespace = $this->getNamespace($this->file->getSmartFileInfo());
        if (! str_starts_with($controller, '\\')) {
            $controller = $namespace . '\\' . $controller;
        }

        return [$controller, $method];
    }

    private function getActionPosition(Identifier|Expr $name): int
    {
        if ($this->routerRegisterNodeAnalyzer->isRegisterFallback($name)) {
            return 0;
        }

        if ($this->routerRegisterNodeAnalyzer->isRegisterMultipleVerbs($name)) {
            return 2;
        }

        return 1;
    }

    /**
     * @param mixed $action
     */
    private function isActionString($action): bool
    {
        if (! is_string($action)) {
            return false;
        }

        return str_contains($action, '@');
    }

    private function getNamespace(SmartFileInfo $fileInfo): string
    {
        $realpath = $fileInfo->getRealPath();
        return $this->routes[$realpath] ?? $this->namespace;
    }
}
