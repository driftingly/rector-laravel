<?php

declare(strict_types=1);

namespace RectorLaravel\Rector\Class_;

use PhpParser\Node;
use PHPStan\Type\ObjectType;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\TraitUse;
use RectorLaravel\AbstractRector;
use PHPStan\Reflection\ReflectionProvider;
use Rector\Comments\NodeDocBlock\DocBlockUpdater;
use PHPStan\PhpDocParser\Ast\PhpDoc\PhpDocTagNode;
use PHPStan\PhpDocParser\Ast\Type\GenericTypeNode;
use PHPStan\PhpDocParser\Ast\PhpDoc\UsesTagValueNode;
use PHPStan\PhpDocParser\Ast\PhpDoc\GenericTagValueNode;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;
use Rector\BetterPhpDocParser\PhpDocInfo\PhpDocInfoFactory;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Rector\BetterPhpDocParser\ValueObject\Type\FullyQualifiedIdentifierTypeNode;

/**
 * @see \RectorLaravel\Tests\Rector\Class_\AddUseAnnotationToHasFactoryTraitRector\AddUseAnnotationToHasFactoryTraitRectorTest
 */
final class AddUseAnnotationToHasFactoryTraitRector extends AbstractRector
{
    private const string USE_TAG_NAME = '@use';

    private const string HAS_FACTORY_TRAIT = 'Illuminate\Database\Eloquent\Factories\HasFactory';

    public function __construct(
        private readonly DocBlockUpdater $docBlockUpdater,
        private readonly PhpDocInfoFactory $phpDocInfoFactory,
        private readonly ReflectionProvider $reflectionProvider,
    ) {}

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(
            'Adds @use annotation to HasFactory trait usage to provide better IDE support.',
            [new CodeSample(
                <<<'CODE_SAMPLE'
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class User extends Model
{
    use HasFactory;
}
CODE_SAMPLE,
                <<<'CODE_SAMPLE'
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class User extends Model
{
    /** @use \Illuminate\Database\Eloquent\Factories\HasFactory<\Database\Factories\UserFactory> */
    use HasFactory;
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

        $hasChanged = false;

        foreach ($node->stmts as $stmt) {
            if (! $stmt instanceof TraitUse) {
                continue;
            }

            if (! $this->hasHasFactoryTrait($stmt)) {
                continue;
            }

            if ($this->addUsePhpDocTag($stmt, $node)) {
                $hasChanged = true;
            }
        }

        if ($hasChanged) {
            return $node;
        }

        return null;
    }

    private function hasHasFactoryTrait(TraitUse $traitUse): bool
    {
        foreach ($traitUse->traits as $trait) {
            $traitName = $this->getName($trait);
            if ($traitName === self::HAS_FACTORY_TRAIT || $traitName === 'HasFactory') {
                return true;
            }
        }

        return false;
    }

    private function addUsePhpDocTag(TraitUse $traitUse, Class_ $class): bool
    {
        $phpDocInfo = $this->phpDocInfoFactory->createFromNodeOrEmpty($traitUse);

        if ($phpDocInfo->hasByName(self::USE_TAG_NAME)) {
            return false;
        }

        $factoryClassName = $this->resolveFactoryClassName($class);
        if ($factoryClassName === null) {
            return false;
        }

        $phpDocTagNode = new PhpDocTagNode(
            self::USE_TAG_NAME,
            new UsesTagValueNode(
                new GenericTypeNode(
                    new FullyQualifiedIdentifierTypeNode(self::HAS_FACTORY_TRAIT),
                    [new FullyQualifiedIdentifierTypeNode($factoryClassName)]
                ),
                ''
            )
        );

        $phpDocInfo->addPhpDocTagNode($phpDocTagNode);

        $this->docBlockUpdater->updateRefactoredNodeWithPhpDocInfo($traitUse);

        return true;
    }

    private function resolveFactoryClassName(Class_ $class): ?string
    {
        $className = $this->getName($class);
        if ($className === null) {
            return null;
        }

        $modelName = $this->nodeNameResolver->getShortName($className);

        $factoryName = $modelName . 'Factory';

        $currentNamespace = $class->namespacedName?->toString() ?? $className;

        $factoryClassNames = $this->getPotentialFactoryClassNames($currentNamespace, $factoryName);

        foreach ($factoryClassNames as $factoryClassName) {
            if ($this->reflectionProvider->hasClass($factoryClassName)) {
                return $factoryClassName;
            }
        }

        return null;
    }

    /**
     * @return string[]
     */
    private function getPotentialFactoryClassNames(string $modelNamespace, string $factoryName): array
    {
        $factoryClassNames = [];

        if (str_contains($modelNamespace, '\\Models\\')) {
            $afterModels = substr($modelNamespace, strpos($modelNamespace, '\\Models\\') + 8);

            if (str_contains($afterModels, '\\')) {
                $namespaceParts = explode('\\', $afterModels);
                array_pop($namespaceParts);
                $deepNamespace = implode('\\', $namespaceParts);

                $factoryClassNames[] = '\\Database\\Factories\\' . $deepNamespace . '\\' . $factoryName;
            }
        } elseif (str_contains($modelNamespace, 'App\\')) {
            $afterApp = substr($modelNamespace, strpos($modelNamespace, 'App\\') + 4);

            if (str_contains($afterApp, '\\')) {
                $namespaceParts = explode('\\', $afterApp);
                array_pop($namespaceParts);
                $deepNamespace = implode('\\', $namespaceParts);

                $factoryClassNames[] = '\\Database\\Factories\\' . $deepNamespace . '\\' . $factoryName;
            }
        }
        $factoryClassNames[] = '\\Database\\Factories\\' . $factoryName;

        return array_unique($factoryClassNames);
    }
}
