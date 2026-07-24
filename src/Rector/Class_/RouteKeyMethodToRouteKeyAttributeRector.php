<?php

declare(strict_types=1);

namespace RectorLaravel\Rector\Class_;

use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Attribute;
use PhpParser\Node\AttributeGroup;
use PhpParser\Node\Name\FullyQualified;
use PhpParser\Node\Scalar\String_;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Return_;
use PHPStan\Type\ObjectType;
use Rector\Php80\NodeAnalyzer\PhpAttributeAnalyzer;
use RectorLaravel\AbstractRector;
use RectorLaravel\Tests\Rector\Class_\RouteKeyMethodToRouteKeyAttributeRector\RouteKeyMethodToRouteKeyAttributeRectorTest;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see RouteKeyMethodToRouteKeyAttributeRectorTest
 */
final class RouteKeyMethodToRouteKeyAttributeRector extends AbstractRector
{
    public function __construct(private readonly PhpAttributeAnalyzer $phpAttributeAnalyzer) {}

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(
            'Changes model getRouteKeyName() method to use the RouteKey attribute',
            [new CodeSample(
                <<<'CODE_SAMPLE'
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
CODE_SAMPLE,
                <<<'CODE_SAMPLE'
use Illuminate\Database\Eloquent\Model;

#[\Illuminate\Database\Eloquent\Attributes\RouteKey('slug')]
class Post extends Model
{
}
CODE_SAMPLE
            )]
        );
    }

    /**
     * @return array<class-string<Node>>
     */
    public function getNodeTypes(): array
    {
        return [Class_::class];
    }

    /**
     * @param  Class_  $node
     */
    public function refactor(Node $node): ?Node
    {
        if (! $this->isObjectType($node, new ObjectType('Illuminate\Database\Eloquent\Model'))) {
            return null;
        }

        if ($this->phpAttributeAnalyzer->hasPhpAttribute($node, 'Illuminate\Database\Eloquent\Attributes\RouteKey')) {
            return null;
        }

        $routeKeyMethod = $node->getMethod('getRouteKeyName');
        if (! $routeKeyMethod instanceof ClassMethod) {
            return null;
        }

        $returnExpr = $this->resolveReturnString($routeKeyMethod);
        if (! $returnExpr instanceof String_) {
            return null;
        }

        $node->attrGroups[] = new AttributeGroup([
            new Attribute(new FullyQualified('Illuminate\Database\Eloquent\Attributes\RouteKey'), [new Arg($returnExpr)]),
        ]);

        foreach ($node->stmts as $key => $stmt) {
            if ($stmt === $routeKeyMethod) {
                unset($node->stmts[$key]);
                break;
            }
        }

        return $node;
    }

    private function resolveReturnString(ClassMethod $classMethod): ?String_
    {
        if ($classMethod->stmts === null || count($classMethod->stmts) !== 1) {
            return null;
        }

        $onlyStmt = $classMethod->stmts[0];
        if (! $onlyStmt instanceof Return_ || ! $onlyStmt->expr instanceof String_) {
            return null;
        }

        return $onlyStmt->expr;
    }
}
