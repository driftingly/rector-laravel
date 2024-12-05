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
use Rector\Rector\AbstractRector;
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
     * @var \Rector\Comments\NodeDocBlock\DocBlockUpdater
     */
    private $docBlockUpdater;
    /**
     * @readonly
     * @var \Rector\BetterPhpDocParser\PhpDocInfo\PhpDocInfoFactory
     */
    private $phpDocInfoFactory;
    private const EXTENDS_TAG_NAME = '@extends';

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

        foreach ($node->stmts as $stmt) {
            if (! $stmt instanceof Property) {
                continue;
            }

            if (! $this->isName($stmt, 'model')) {
                continue;
            }

            $this->addExtendsPhpDocTag($node, $stmt);

            $this->docBlockUpdater->updateRefactoredNodeWithPhpDocInfo($node);

            break;
        }

        return $node;
    }

    public function addExtendsPhpDocTag(Node $node, Property $property): void
    {
        if ($property->props === []) {
            return;
        }

        $modelName = $this->getModelName($property->props[0]->default);

        if ($modelName === null) {
            return;
        }

        $phpDocInfo = $this->phpDocInfoFactory->createFromNodeOrEmpty($node);

        if ($phpDocInfo->hasByName(self::EXTENDS_TAG_NAME)) {
            return;
        }

        $phpDocTagNode = new PhpDocTagNode(self::EXTENDS_TAG_NAME, new ExtendsTagValueNode(
            new GenericTypeNode(
                new FullyQualifiedIdentifierTypeNode(self::FACTORY_CLASS_NAME),
                [new FullyQualifiedIdentifierTypeNode($modelName)]
            ),
            ''
        ));

        $phpDocInfo->addPhpDocTagNode($phpDocTagNode);
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
