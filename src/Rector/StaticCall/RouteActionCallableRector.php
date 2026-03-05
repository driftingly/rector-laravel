<?php

declare(strict_types=1);

namespace RectorLaravel\Rector\StaticCall;

use Illuminate\Support\Facades\Route;
use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\ArrayItem;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\Array_;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Identifier;
use PhpParser\Node\Scalar\String_;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\MethodReflection;
use Rector\Contract\Rector\ConfigurableRectorInterface;
use Rector\NodeTypeResolver\Node\AttributeKey;
use Rector\PhpParser\Node\Value\ValueResolver;
use Rector\Reflection\ReflectionResolver;
use RectorLaravel\AbstractRector;
use RectorLaravel\NodeFactory\RouterRegisterNodeAnalyzer;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\ConfiguredCodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;
use Webmozart\Assert\Assert;

/**
 * @changelog https://laravel.com/docs/8.x/upgrade#automatic-controller-namespace-prefixing
 *
 * @see \RectorLaravel\Tests\Rector\StaticCall\RouteActionCallableRector\RouteActionCallableRectorTest
 */
final class RouteActionCallableRector extends AbstractRector implements ConfigurableRectorInterface
{
    /**
     * @readonly
     */
    private ReflectionResolver $reflectionResolver;
    /**
     * @readonly
     */
    private RouterRegisterNodeAnalyzer $routerRegisterNodeAnalyzer;
    /**
     * @readonly
     */
    private ValueResolver $valueResolver;
    /**
     * @var string
     */
    public const ROUTES = 'routes';

    /**
     * @var string
     */
    public const NAMESPACE = 'namespace';

    /**
     * @var string
     */
    public const NAMESPACE_ATTRIBUTE = 'laravel_route_group_namespace';

    /**
     * @var string
     */
    private const DEFAULT_NAMESPACE = 'App\Http\Controllers';

    private string $namespace = self::DEFAULT_NAMESPACE;

    /**
     * @var array<string, string>
     */
    private array $routes = [];

    public function __construct(ReflectionResolver $reflectionResolver, RouterRegisterNodeAnalyzer $routerRegisterNodeAnalyzer, ValueResolver $valueResolver)
    {
        $this->reflectionResolver = $reflectionResolver;
        $this->routerRegisterNodeAnalyzer = $routerRegisterNodeAnalyzer;
        $this->valueResolver = $valueResolver;
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Use PHP callable syntax instead of string syntax for controller route declarations.', [
            new ConfiguredCodeSample(
                <<<'CODE_SAMPLE'
Route::get('/users', 'UserController@index');

Route::group(['namespace' => 'Admin'], function () {
    Route::get('/users', 'UserController@index');
})
CODE_SAMPLE

                ,
                <<<'CODE_SAMPLE'
Route::get('/users', [\App\Http\Controllers\UserController::class, 'index']);

Route::group(['namespace' => 'Admin'], function () {
    Route::get('/users', [\App\Http\Controllers\Admin\UserController::class, 'index']);
})
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
     * @param  MethodCall|StaticCall  $node
     * @return \PhpParser\Node\Expr\MethodCall|\PhpParser\Node\Expr\StaticCall|null
     */
    public function refactor(Node $node)
    {
        if ($this->routerRegisterNodeAnalyzer->isGroup($node->name)) {
            if (! isset($node->args[1]) || ! $node->args[1] instanceof Arg) {
                return null;
            }

            $namespace = $this->routerRegisterNodeAnalyzer->getGroupNamespace($node);

            $groupNamespace = $node->getAttribute(self::NAMESPACE_ATTRIBUTE);

            // if the route is in a namespace but can't be resolved to a value, don't continue
            if (! is_string($groupNamespace) && ! is_null($groupNamespace)) {
                return null;
            }

            if (is_string($groupNamespace)) {
                $namespace = $groupNamespace . '\\' . $namespace;
            }

            $this->traverseNodesWithCallable($node->args[1]->value, function (Node $node) use ($namespace) {
                if (! $node instanceof MethodCall && ! $node instanceof StaticCall) {
                    return null;
                }

                if (
                    $this->routerRegisterNodeAnalyzer->isRegisterMethodStaticCall($node) ||
                    $this->routerRegisterNodeAnalyzer->isGroup($node->name)
                ) {
                    $node->setAttribute(self::NAMESPACE_ATTRIBUTE, $namespace);
                }

                return null;
            });

            return null;
        }

        if (! $this->routerRegisterNodeAnalyzer->isRegisterMethodStaticCall($node)) {
            return null;
        }

        $groupNamespace = $node->getAttribute(self::NAMESPACE_ATTRIBUTE);

        // if the route is in a namespace but can't be resolved to a value, don't continue
        if (! is_string($groupNamespace) && ! is_null($groupNamespace)) {
            return null;
        }

        $position = $this->getActionPosition($node->name);

        if (! isset($node->args[$position])) {
            return null;
        }

        if (! $node->args[$position] instanceof Arg) {
            return null;
        }

        $arg = $node->args[$position];

        $argValue = $this->valueResolver->getValue($arg->value);
        $segments = $this->resolveControllerFromAction($argValue, $groupNamespace);
        if ($segments === null) {
            return null;
        }

        $scope = $node->getAttribute(AttributeKey::SCOPE);
        if (! $scope instanceof Scope) {
            return null;
        }

        $phpMethodReflection = $this->reflectionResolver->resolveMethodReflection($segments[0], $segments[1], $scope);

        if (! $phpMethodReflection instanceof MethodReflection) {
            return null;
        }

        $node->args[$position]->value = $this->nodeFactory->createArray([
            $this->nodeFactory->createClassConstReference($segments[0]),
            $segments[1],
        ]);

        if (is_array($argValue) && isset($argValue['as']) && is_string($argValue['as'])) {
            $node = new MethodCall($node, 'name', [new Arg(new String_($argValue['as']))]);
        }

        if (
            is_array($argValue)
            && isset($argValue['middleware'])
            && (is_string($argValue['middleware']) || is_array($argValue['middleware']))
        ) {
            if (is_string($argValue['middleware'])) {
                $argument = new String_($argValue['middleware']);
            } else {
                // if any of the elements in the middleware array is not a string, return node as is
                if (array_filter($argValue['middleware'], static fn ($value) => ! is_string($value)) !== []) {
                    return $node;
                }

                /** @var list<string> $middleware */
                $middleware = $argValue['middleware'];

                $argument = new Array_(array_map(
                    static fn ($value) => new ArrayItem(new String_($value)),
                    $middleware
                ));
            }
            $node = new MethodCall($node, 'middleware', [new Arg($argument)]);
        }

        return $node;
    }

    /**
     * @param  mixed[]  $configuration
     */
    public function configure(array $configuration): void
    {
        $routes = $configuration[self::ROUTES] ?? [];
        Assert::isArray($routes);
        Assert::allString(array_keys($routes));
        Assert::allString($routes);
        /** @var array<string, string> $routes */
        $this->routes = $routes;

        $namespace = $configuration[self::NAMESPACE] ?? self::DEFAULT_NAMESPACE;
        Assert::string($namespace);
        $this->namespace = $namespace;
    }

    /**
     * @return array{string, string}|null
     * @param mixed $action
     */
    private function resolveControllerFromAction($action, ?string $groupNamespace = null): ?array
    {
        if (! $this->isActionString($action)) {
            return null;
        }

        /** @var string|array<string, string> $action */
        $segments = is_string($action)
            ? explode('@', $action)
            : explode('@', $action['uses']);

        if (count($segments) !== 2) {
            return null;
        }

        [$controller, $method] = $segments;
        $namespace = $this->getNamespace($this->file->getFilePath());
        if ($groupNamespace !== null) {
            $namespace .= '\\' . $groupNamespace;
        }
        if (strncmp($controller, '\\', strlen('\\')) !== 0) {
            $controller = $namespace . '\\' . $controller;
        }

        return [$controller, $method];
    }

    /**
     * @param \PhpParser\Node\Identifier|\PhpParser\Node\Expr $name
     */
    private function getActionPosition($name): int
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
            if (! is_array($action)) {
                return false;
            }

            $keys = array_keys($action);
            sort($keys);

            return in_array('uses', $keys, true) && array_diff($keys, ['as', 'middleware', 'uses']) === [];
        }

        return strpos($action, '@') !== false;
    }

    private function getNamespace(string $filePath): string
    {
        return $this->routes[$filePath] ?? $this->namespace;
    }
}
