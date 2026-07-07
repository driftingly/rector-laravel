<?php

declare(strict_types=1);

namespace RectorLaravel\Rector\ClassMethod;

use PhpParser\Node;
use PhpParser\Node\Name;
use PhpParser\Node\Stmt\ClassMethod;
use PHPStan\Analyser\Scope;
use PHPStan\PhpDocParser\Ast\PhpDoc\ParamTagValueNode;
use PHPStan\PhpDocParser\Ast\PhpDoc\ReturnTagValueNode;
use PHPStan\PhpDocParser\Ast\Type\GenericTypeNode;
use PHPStan\PhpDocParser\Ast\Type\IdentifierTypeNode;
use PHPStan\PhpDocParser\Ast\Type\TypeNode;
use PHPStan\Reflection\ClassReflection;
use PHPStan\Reflection\ReflectionProvider;
use PHPStan\Type\Generic\GenericObjectType;
use PHPStan\Type\ObjectType;
use Rector\BetterPhpDocParser\PhpDocInfo\PhpDocInfo;
use Rector\BetterPhpDocParser\PhpDocInfo\PhpDocInfoFactory;
use Rector\BetterPhpDocParser\ValueObject\Type\FullyQualifiedIdentifierTypeNode;
use Rector\Comments\NodeDocBlock\DocBlockUpdater;
use Rector\NodeTypeResolver\TypeComparator\TypeComparator;
use Rector\PHPStan\ScopeFetcher;
use Rector\StaticTypeMapper\StaticTypeMapper;
use RectorLaravel\AbstractRector;
use RectorLaravel\NodeAnalyzer\ScopeAnalyzer;
use RectorLaravel\Tests\Rector\ClassMethod\AddGenericBuilderToScopesRector\AddGenericBuilderToScopesRectorTest;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see AddGenericBuilderToScopesRectorTest
 */
class AddGenericBuilderToScopesRector extends AbstractRector
{
    public function __construct(
        private readonly TypeComparator $typeComparator,
        private readonly PhpDocInfoFactory $phpDocInfoFactory,
        private readonly DocBlockUpdater $docBlockUpdater,
        private readonly StaticTypeMapper $staticTypeMapper,
        private readonly ReflectionProvider $reflectionProvider,
        private readonly ScopeAnalyzer $scopeAnalyzer,
    ) {}

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(
            'Add generic Builder return type to scopes in child of Illuminate\Database\Eloquent\Model',
            [
                new CodeSample(
                    <<<'CODE_SAMPLE'
use App\Post;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Post extends Model
{
    public function scopePopular(Builder $query): Builder
    {
        return $query->where('votes', '>', 100);
    }
}
CODE_SAMPLE
                    ,
                    <<<'CODE_SAMPLE'
use App\Post;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Post extends Model
{
    /**
     * @param \Illuminate\Database\Eloquent\Builder<static> $query
     * @return \Illuminate\Database\Eloquent\Builder<static>
     */
    public function scopePopular(Builder $query): Builder
    {
        return $query->where('votes', '>', 100);
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

    public function refactor(Node $node): ?Node
    {
        if (! $node instanceof ClassMethod) {
            return null;
        }

        $scope = ScopeFetcher::fetch($node);

        if ($this->shouldSkipNode($node, $scope)) {
            return null;
        }

        if (! $this->scopeAnalyzer->isScopeMethod($node)) {
            return null;
        }

        if (count($node->params) < 1) {
            return null;
        }

        $classReflection = $scope->getClassReflection();

        if (! $classReflection instanceof ClassReflection) {
            return null;
        }

        $firstParam = $node->params[0];
        $paramName = $this->getName($firstParam->var);

        if ($paramName === null) {
            return null;
        }

        $phpDocInfo = $this->phpDocInfoFactory->createFromNodeOrEmpty($node);

        $hasChanged = $this->refactorParam($phpDocInfo, $paramName, $classReflection);
        $hasChanged = $this->refactorReturn($node, $phpDocInfo, $classReflection) || $hasChanged;

        if (! $hasChanged) {
            return null;
        }

        $this->docBlockUpdater->updateRefactoredNodeWithPhpDocInfo($node);

        return $node;
    }

    private function refactorParam(
        PhpDocInfo $phpDocInfo,
        string $paramName,
        ClassReflection $classReflection
    ): bool {
        $genericTypeNode = $this->createBuilderGenericTypeNode($classReflection);

        $existingParamTag = $phpDocInfo->getParamTagValueByName($paramName);

        if ($existingParamTag instanceof ParamTagValueNode) {
            if ($this->isGenericTypeAlreadyCorrect($existingParamTag->type, $classReflection)) {
                return false;
            }

            $existingParamTag->type = $genericTypeNode;

            return true;
        }

        $phpDocInfo->addTagValueNode(new ParamTagValueNode(
            $genericTypeNode,
            false,
            '$' . $paramName,
            '',
            false
        ));

        return true;
    }

    private function refactorReturn(
        ClassMethod $classMethod,
        PhpDocInfo $phpDocInfo,
        ClassReflection $classReflection
    ): bool {
        $methodReturnType = $classMethod->getReturnType();

        if (! $methodReturnType instanceof Name) {
            return false;
        }

        if (! $this->isObjectType($methodReturnType, new ObjectType('Illuminate\Database\Eloquent\Builder'))) {
            return false;
        }

        $genericTypeNode = $this->createBuilderGenericTypeNode($classReflection);

        $existingReturnTag = $phpDocInfo->getReturnTagValue();

        if ($existingReturnTag instanceof ReturnTagValueNode) {
            if ($this->isGenericTypeAlreadyCorrect($existingReturnTag->type, $classReflection)) {
                return false;
            }

            $existingReturnTag->type = $genericTypeNode;

            return true;
        }

        if ($this->areNativeTypeAndPhpDocReturnTypeDifferent($classMethod, $methodReturnType, $phpDocInfo)) {
            return false;
        }

        $phpDocInfo->addTagValueNode(new ReturnTagValueNode($genericTypeNode, ''));

        return true;
    }

    private function shouldSkipNode(ClassMethod $classMethod, Scope $scope): bool
    {
        if ($classMethod->stmts === null) {
            return true;
        }

        $classReflection = $scope->getClassReflection();

        if (! $classReflection instanceof ClassReflection || $classReflection->isAnonymous()) {
            return true;
        }

        return ! $classReflection->isSubclassOfClass(
            $this->reflectionProvider->getClass('Illuminate\Database\Eloquent\Model')
        );
    }

    private function isGenericTypeAlreadyCorrect(
        TypeNode $typeNode,
        ClassReflection $classReflection
    ): bool {
        if (! $typeNode instanceof GenericTypeNode) {
            return false;
        }

        if (count($typeNode->genericTypes) !== 1) {
            return false;
        }

        $param = $typeNode->genericTypes[0];
        $expectedName = $classReflection->isFinal() ? 'self' : 'static';

        return $param instanceof IdentifierTypeNode && $param->name === $expectedName;
    }

    private function createBuilderGenericTypeNode(ClassReflection $classReflection): GenericTypeNode
    {
        $typeParam = $classReflection->isFinal()
            ? new IdentifierTypeNode('self')
            : new IdentifierTypeNode('static');

        return new GenericTypeNode(
            new FullyQualifiedIdentifierTypeNode('Illuminate\Database\Eloquent\Builder'),
            [$typeParam]
        );
    }

    private function areNativeTypeAndPhpDocReturnTypeDifferent(
        ClassMethod $classMethod,
        Node $methodReturnType,
        PhpDocInfo $phpDocInfo
    ): bool {
        $returnTagValue = $phpDocInfo->getReturnTagValue();

        if (! $returnTagValue instanceof ReturnTagValueNode) {
            return false;
        }

        $phpDocPHPStanType = $this->staticTypeMapper->mapPHPStanPhpDocTypeNodeToPHPStanType(
            $returnTagValue->type,
            $classMethod
        );

        $phpDocPHPStanTypeWithoutGenerics = $phpDocPHPStanType;
        if ($phpDocPHPStanType instanceof GenericObjectType) {
            $phpDocPHPStanTypeWithoutGenerics = new ObjectType($phpDocPHPStanType->getClassName());
        }

        $methodReturnTypePHPStanType = $this->staticTypeMapper->mapPhpParserNodePHPStanType($methodReturnType);

        return ! $this->typeComparator->areTypesEqual(
            $methodReturnTypePHPStanType,
            $phpDocPHPStanTypeWithoutGenerics,
        );
    }
}
