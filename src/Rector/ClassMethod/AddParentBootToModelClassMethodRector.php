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
 * @changelog https://laracasts.com/discuss/channels/laravel/laravel-57-upgrade-observer-problem
 *
 * @see \RectorLaravel\Tests\Rector\ClassMethod\AddParentBootToModelClassMethodRector\AddParentBootToModelClassMethodRectorTest
 */
final class AddParentBootToModelClassMethodRector extends AbstractRector
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
    private const BOOT = 'boot';

    public function __construct(StaticCallAnalyzer $staticCallAnalyzer, ReflectionResolver $reflectionResolver)
    {
        $this->staticCallAnalyzer = $staticCallAnalyzer;
        $this->reflectionResolver = $reflectionResolver;
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(
            'Add parent::boot(); call to boot() class method in child of Illuminate\Database\Eloquent\Model',
            [
                new CodeSample(
                    <<<'CODE_SAMPLE'
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    public function boot()
    {
    }
}
CODE_SAMPLE

                    ,
                    <<<'CODE_SAMPLE'
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    public function boot()
    {
        parent::boot();
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

        if (! $classReflection->isSubclassOf('Illuminate\Database\Eloquent\Model')) {
            return null;
        }

        if (! $this->isName($node->name, self::BOOT)) {
            return null;
        }

        foreach ((array) $node->stmts as $key => $classMethodStmt) {
            if ($classMethodStmt instanceof Expression) {
                $classMethodStmt = $classMethodStmt->expr;
            }

            // is in the 1st position? → only correct place
            // @see https://laracasts.com/discuss/channels/laravel/laravel-57-upgrade-observer-problem?page=0#reply=454409
            if (! $this->staticCallAnalyzer->isParentCallNamed($classMethodStmt, self::BOOT)) {
                continue;
            }

            if ($key === 0) {
                return null;
            }

            // wrong location → remove it
            unset($node->stmts[$key]);
        }

        // missing, we need to add one
        $staticCall = $this->nodeFactory->createStaticCall('parent', self::BOOT);
        $parentStaticCallExpression = new Expression($staticCall);

        $node->stmts = array_merge([$parentStaticCallExpression], (array) $node->stmts);

        return $node;
    }
}
