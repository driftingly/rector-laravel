<?php

declare(strict_types=1);

namespace Rector\Laravel\Rector\StaticCall;

use PhpParser\Node;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Identifier;
use PHPStan\Reflection\Php\PhpMethodReflection;
use PHPStan\Type\ObjectType;
use Rector\Core\Contract\Rector\ConfigurableRectorInterface;
use Rector\Core\Rector\AbstractRector;
use Rector\Core\Reflection\ReflectionResolver;
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
        if (! $this->isRouteAdditionMethod($node)) {
            return null;
        }

        $position = $this->getActionPosition($node->name);

        if (! isset($node->args[$position])) {
            return null;
        }

        $arg = $node->args[$position];

        $action = $this->valueResolver->getValue($arg->value);
        if (! $this->isActionString($action)) {
            return null;
        }

        $segments = explode('@', $action);
        if (count($segments) !== 2) {
            return null;
        }

        [$controller,$method] = $segments;
        $namespace = $this->getNamespace($this->file->getSmartFileInfo());
        if (! str_starts_with($controller, '\\')) {
            $controller = $namespace . '\\' . $controller;
        }

        $scope = $node->getAttribute(AttributeKey::SCOPE);

        $phpMethodReflection = $this->reflectionResolver->resolveMethodReflection($controller, $method, $scope);

        if (! $phpMethodReflection instanceof PhpMethodReflection) {
            return null;
        }

        $node->args[$position]->value = $this->nodeFactory->createArray([
            $this->nodeFactory->createClassConstReference($controller),
            $method,
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

    private function isRouteAdditionMethod(MethodCall|StaticCall $node): bool
    {
        if (! $this->isNames(
            $node->name,
            ['any', 'delete', 'get', 'options', 'patch', 'post', 'put', 'match', 'fallback']
        )) {
            return false;
        }

        if ($node instanceof MethodCall && $this->nodeTypeResolver->isObjectTypes(
            $node->var,
            [new ObjectType('Illuminate\Routing\Router'), new ObjectType('Illuminate\Routing\RouteRegistrar')]
        )) {
            return true;
        }

        return $node instanceof StaticCall && $this->isName($node->class, 'Illuminate\Support\Facades\Route');
    }

    private function getActionPosition(Identifier|Expr $name): int
    {
        if ($this->isName($name, 'fallback')) {
            return 0;
        }

        if ($this->isName($name, 'match')) {
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
