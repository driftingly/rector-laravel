<?php

declare(strict_types=1);

namespace RectorLaravel\Rector\Class_;

use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Attribute;
use PhpParser\Node\AttributeGroup;
use PhpParser\Node\Expr\ClassConstFetch;
use PhpParser\Node\Expr\New_;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Return_;
use PhpParser\Node\Name\FullyQualified;
use PhpParser\Node\Stmt\Class_;
use PHPStan\Type\ObjectType;
use Rector\Php80\NodeAnalyzer\PhpAttributeAnalyzer;
use RectorLaravel\AbstractRector;
use RectorLaravel\Tests\Rector\Class_\CollectedByPropertyToCollectedByAttributeRector\CollectedByPropertyToCollectedByAttributeRectorTest;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see CollectedByPropertyToCollectedByAttributeRectorTest
 */
final class CollectedByPropertyToCollectedByAttributeRector extends AbstractRector
{
    public function __construct(private readonly PhpAttributeAnalyzer $phpAttributeAnalyzer) {}

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(
            'Changes model newCollection method to use the CollectedBy attribute',
            [new CodeSample(
                <<<'CODE_SAMPLE'
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;
use App\Collections\UserCollection;

class User extends Model
{
    /**
     * @param  array<int, \Illuminate\Database\Eloquent\Model>  $models
     * @return \Illuminate\Database\Eloquent\Collection<int, \Illuminate\Database\Eloquent\Model>
     */
    public function newCollection(array $models = []): Collection
    {
        return new UserCollection($models);
    }
}
CODE_SAMPLE,
                <<<'CODE_SAMPLE'
use Illuminate\Database\Eloquent\Model;
use App\Collections\UserCollection;
use Illuminate\Database\Eloquent\Attributes\CollectedBy;

#[CollectedBy(UserCollection::class)]
class User extends Model
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

        if ($this->phpAttributeAnalyzer->hasPhpAttribute($node, 'Illuminate\Database\Eloquent\Attributes\CollectedBy')) {
            return null;
        }

        $newCollectionMethod = $node->getMethod('newCollection');
        if (! $newCollectionMethod instanceof ClassMethod) {
            return null;
        }

        $value = $this->resolveCollectedByValue($newCollectionMethod);
        if (! $value instanceof ClassConstFetch) {
            return null;
        }

        $node->attrGroups[] = new AttributeGroup([
            new Attribute(
                new FullyQualified('Illuminate\Database\Eloquent\Attributes\CollectedBy'),
                [new Arg($value)]
            ),
        ]);

        foreach ($node->stmts as $key => $stmt) {
            if ($stmt === $newCollectionMethod) {
                unset($node->stmts[$key]);
                break;
            }
        }

        return $node;
    }

    private function resolveCollectedByValue(ClassMethod $classMethod): ?ClassConstFetch
    {
        if ($classMethod->stmts === null || count($classMethod->stmts) !== 1) {
            return null;
        }

        $onlyStmt = $classMethod->stmts[0];
        if (! $onlyStmt instanceof Return_ || ! $onlyStmt->expr instanceof New_) {
            return null;
        }

        $newClass = $onlyStmt->expr->class;
        if (! $newClass instanceof Node\Name) {
            return null;
        }

        return new ClassConstFetch($newClass, 'class');
    }
}
