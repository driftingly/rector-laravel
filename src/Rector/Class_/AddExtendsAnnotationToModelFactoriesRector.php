<?php

declare(strict_types=1);

namespace RectorLaravel\Rector\Class_;

use PhpParser\Node;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\ClassConstFetch;
use PhpParser\Node\Scalar\String_;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\Property;
use PHPStan\PhpDocParser\Ast\PhpDoc\ExtendsTagValueNode;
use PHPStan\PhpDocParser\Ast\PhpDoc\PhpDocTagNode;
use PHPStan\PhpDocParser\Ast\Type\GenericTypeNode;
use PHPStan\Type\ObjectType;
use Rector\BetterPhpDocParser\PhpDocInfo\PhpDocInfoFactory;
use Rector\BetterPhpDocParser\ValueObject\Type\FullyQualifiedIdentifierTypeNode;
use Rector\Comments\NodeDocBlock\DocBlockUpdater;
use RectorLaravel\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @changelog https://github.com/laravel/framework/pull/39169
 *
 * @see \RectorLaravel\Tests\Rector\Class_\AddExtendsAnnotationToModelFactoriesRector\AddExtendsAnnotationToModelFactoriesRectorTest
 */
final class AddExtendsAnnotationToModelFactoriesRector extends AbstractRector
{
    /**
     * @readonly
     */
    private DocBlockUpdater $docBlockUpdater;
    /**
     * @readonly
     */
    private PhpDocInfoFactory $phpDocInfoFactory;
    /**
     * @var string
     */
    private const EXTENDS_TAG_NAME = '@extends';

    /**
     * @var string
     */
    private const FACTORY_CLASS_NAME = 'Illuminate\Database\Eloquent\Factories\Factory';

    public function __construct(DocBlockUpdater $docBlockUpdater, PhpDocInfoFactory $phpDocInfoFactory)
    {
        $this->docBlockUpdater = $docBlockUpdater;
        $this->phpDocInfoFactory = $phpDocInfoFactory;
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Adds the @extends annotation to Factories.', [
            new CodeSample(
                <<<'CODE_SAMPLE'
use Illuminate\Database\Eloquent\Factories\Factory;

class UserFactory extends Factory
{
    protected $model = \App\Models\User::class;
}
CODE_SAMPLE

                ,
                <<<'CODE_SAMPLE'
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    protected $model = \App\Models\User::class;
}
CODE_SAMPLE
            ),
        ]);
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
        if (! $this->isObjectType($node, new ObjectType(self::FACTORY_CLASS_NAME))) {
            return null;
        }

        $hasChanged = false;

        foreach ($node->stmts as $stmt) {
            if (! $stmt instanceof Property) {
                continue;
            }

            if (! $this->isName($stmt, 'model')) {
                continue;
            }

            $hasChanged = $this->addExtendsPhpDocTag($node, $stmt);

            break;
        }

        if ($hasChanged) {
            $this->docBlockUpdater->updateRefactoredNodeWithPhpDocInfo($node);

            return $node;
        }

        return null;
    }

    private function addExtendsPhpDocTag(Node $node, Property $property): bool
    {
        if ($property->props === []) {
            return false;
        }

        $modelName = $this->getModelName($property->props[0]->default);

        if ($modelName === null) {
            return false;
        }

        $phpDocInfo = $this->phpDocInfoFactory->createFromNodeOrEmpty($node);

        if ($phpDocInfo->hasByName(self::EXTENDS_TAG_NAME)) {
            return false;
        }

        $phpDocTagNode = new PhpDocTagNode(self::EXTENDS_TAG_NAME, new ExtendsTagValueNode(
            new GenericTypeNode(
                new FullyQualifiedIdentifierTypeNode(self::FACTORY_CLASS_NAME),
                [new FullyQualifiedIdentifierTypeNode($modelName)]
            ),
            ''
        ));

        $phpDocInfo->addPhpDocTagNode($phpDocTagNode);

        return true;
    }

    private function getModelName(?Expr $expr): ?string
    {
        if ($expr instanceof ClassConstFetch) {
            return $this->getName($expr->class);
        }

        if ($expr instanceof String_) {
            return $expr->value;
        }

        return null;
    }
}
