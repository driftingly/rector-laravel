<?php

declare(strict_types=1);

namespace RectorLaravel\Rector\ClassMethod;

use PhpParser\Node;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Expression;
use PHPStan\Reflection\ClassReflection;
use Rector\Rector\AbstractRector;
use Rector\Reflection\ReflectionResolver;
use RectorLaravel\NodeAnalyzer\StaticCallAnalyzer;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @changelog https://laravel.com/docs/8.x/upgrade#the-event-service-provider-class
 *
 * @see \RectorLaravel\Tests\Rector\ClassMethod\AddParentRegisterToEventServiceProviderRector\AddParentRegisterToEventServiceProviderRectorTest
 */
final class AddParentRegisterToEventServiceProviderRector extends AbstractRector
{
    /**
     * @readonly
     * @var \RectorLaravel\NodeAnalyzer\StaticCallAnalyzer
     */
    private $staticCallAnalyzer;
    /**
     * @readonly
     * @var \Rector\Reflection\ReflectionResolver
     */
    private $reflectionResolver;
    /**
     * @var string
     */
    private const REGISTER = 'register';

    public function __construct(StaticCallAnalyzer $staticCallAnalyzer, ReflectionResolver $reflectionResolver)
    {
        $this->staticCallAnalyzer = $staticCallAnalyzer;
        $this->reflectionResolver = $reflectionResolver;
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(
            'Add parent::register(); call to register() class method in child of Illuminate\Foundation\Support\Providers\EventServiceProvider',
            [
                new CodeSample(
                    <<<'CODE_SAMPLE'
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    public function register()
    {
    }
}
CODE_SAMPLE

                    ,
                    <<<'CODE_SAMPLE'
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    public function register()
    {
        parent::register();
    }
}
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
        return [ClassMethod::class];
    }

    /**
     * @param  ClassMethod  $node
     */
    public function refactor(Node $node): ?Node
    {
        $classReflection = $this->reflectionResolver->resolveClassReflection($node);

        if (! $classReflection instanceof ClassReflection) {
            return null;
        }

        if (! $classReflection->isSubclassOf('Illuminate\Foundation\Support\Providers\EventServiceProvider')) {
            return null;
        }

        if (! $this->isName($node->name, self::REGISTER)) {
            return null;
        }

        foreach ((array) $node->stmts as $key => $classMethodStmt) {
            if ($classMethodStmt instanceof Expression) {
                $classMethodStmt = $classMethodStmt->expr;
            }

            if (! $this->staticCallAnalyzer->isParentCallNamed($classMethodStmt, self::REGISTER)) {
                continue;
            }

            if ($key === 0) {
                return null;
            }

            unset($node->stmts[$key]);
        }

        $staticCall = $this->nodeFactory->createStaticCall('parent', self::REGISTER);
        $parentStaticCallExpression = new Expression($staticCall);

        $node->stmts = array_merge([$parentStaticCallExpression], (array) $node->stmts);

        return $node;
    }
}
