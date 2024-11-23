<?php

declare(strict_types=1);

namespace RectorLaravel\Rector\StaticCall;

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
use Rector\Rector\AbstractRector;
use Rector\Reflection\ReflectionResolver;
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
     * @var string
     */
    final public const ROUTES = 'routes';

    /**
     * @var string
     */
    final public const NAMESPACE = 'namespace';

    /**
     * @var string
     */
    private const DEFAULT_NAMESPACE = 'App\Http\Controllers';

    private string $namespace = self::DEFAULT_NAMESPACE;

    /**
     * @var array<string, string>
     */
    private array $routes = [];

    public function __construct(
        private readonly ReflectionResolver $reflectionResolver,
        private readonly RouterRegisterNodeAnalyzer $routerRegisterNodeAnalyzer,
        private readonly ValueResolver $valueResolver,
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
     * @param  MethodCall|StaticCall  $node
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

        if (! $node->args[$position] instanceof Arg) {
            return null;
        }

        $arg = $node->args[$position];

        $argValue = $this->valueResolver->getValue($arg->value);
        $segments = $this->resolveControllerFromAction($argValue);
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
     */
    private function resolveControllerFromAction(mixed $action): ?array
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

    private function isActionString(mixed $action): bool
    {
        if (! is_string($action)) {
            if (! is_array($action)) {
                return false;
            }

            $keys = array_keys($action);
            sort($keys);

            return in_array('uses', $keys, true) && array_diff($keys, ['as', 'middleware', 'uses']) === [];
        }

        return str_contains($action, '@');
    }

    private function getNamespace(string $filePath): string
    {
        return $this->routes[$filePath] ?? $this->namespace;
    }
}
