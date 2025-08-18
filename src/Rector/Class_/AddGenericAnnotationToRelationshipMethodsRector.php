<?php

declare(strict_types=1);

namespace RectorLaravel\Rector\Class_;

use PhpParser\Node;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Return_;
use PHPStan\Analyser\Scope;
use PHPStan\PhpDocParser\Ast\PhpDoc\ReturnTagValueNode;
use PHPStan\PhpDocParser\Ast\Type\GenericTypeNode;
use PHPStan\PhpDocParser\Ast\Type\IdentifierTypeNode;
use PHPStan\Reflection\ClassReflection;
use PHPStan\Reflection\ReflectionProvider;
use PHPStan\Type\Generic\GenericObjectType;
use PHPStan\Type\ObjectType;
use PHPStan\Type\ThisType;
use Rector\BetterPhpDocParser\PhpDocInfo\PhpDocInfoFactory;
use Rector\BetterPhpDocParser\ValueObject\Type\FullyQualifiedIdentifierTypeNode;
use Rector\Comments\NodeDocBlock\DocBlockUpdater;
use Rector\NodeTypeResolver\TypeComparator\TypeComparator;
use Rector\PhpParser\Node\BetterNodeFinder;
use Rector\PHPStan\ScopeFetcher;
use Rector\StaticTypeMapper\StaticTypeMapper;
use RectorLaravel\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \RectorLaravel\Tests\Rector\Class_\AddGenericAnnotationToRelationshipMethodsRector\AddGenericAnnotationToRelationshipMethodsRectorTest
 */
final class AddGenericAnnotationToRelationshipMethodsRector extends AbstractRector
{
    private const array RELATIONSHIP_METHODS = [
        'belongsTo' => [],
        'hasOne' => [],
        'hasMany' => [],
        'hasOneThrough' => ['intermediate' => 1],
        'hasManyThrough' => ['intermediate' => 1],
        'belongsToMany' => ['pivot' => '\Illuminate\Database\Eloquent\Relations\Pivot'],
        'morphTo' => [],
        'morphOne' => [],
        'morphMany' => [],
        'morphToMany' => ['pivot' => '\Illuminate\Database\Eloquent\Relations\MorphPivot'],
        'morphedByMany' => ['pivot' => '\Illuminate\Database\Eloquent\Relations\MorphPivot'],
    ];

    public function __construct(
        private readonly DocBlockUpdater $docBlockUpdater,
        private readonly PhpDocInfoFactory $phpDocInfoFactory,
        private readonly BetterNodeFinder $betterNodeFinder,
        private readonly TypeComparator $typeComparator,
        private readonly StaticTypeMapper $staticTypeMapper,
        private readonly ReflectionProvider $reflectionProvider,
    ) {}

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(
            'Add generic PHPDoc annotations to Laravel model relationship methods with proper type templates',
            [
                new CodeSample(
                    <<<'CODE_SAMPLE'
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Comment extends Model
{
    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
CODE_SAMPLE,
                    <<<'CODE_SAMPLE'
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Comment extends Model
{
    /**
     * @return BelongsTo<User, $this>
     */
    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class);
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
        if (! $node instanceof ClassMethod) {
            return null;
        }

        $scope = ScopeFetcher::fetch($node);

        if ($this->shouldSkipMethod($node, $scope)) {
            return null;
        }

        $relationshipCall = $this->getRelationshipMethodCall($node);
        if ($relationshipCall === null) {
            return null;
        }

        $relationshipMethod = $this->getName($relationshipCall->name);
        if (! isset(self::RELATIONSHIP_METHODS[$relationshipMethod])) {
            return null;
        }

        $relatedModel = $this->getRelatedModelClass($relationshipCall);
        if ($relatedModel === null) {
            return null;
        }

        $returnType = $node->getReturnType();
        if ($returnType === null) {
            return null;
        }

        $returnTypeName = $this->getName($returnType);
        if ($returnTypeName === null) {
            return null;
        }

        $phpDocInfo = $this->phpDocInfoFactory->createFromNodeOrEmpty($node);
        if ($this->hasValidGenericAnnotation($phpDocInfo, $relationshipMethod, $relatedModel, $node)) {
            return null;
        }

        $genericTypes = $this->buildGenericTypes($relationshipCall, $relationshipMethod, $relatedModel);
        $genericTypeNode = new GenericTypeNode(
            new FullyQualifiedIdentifierTypeNode($returnTypeName),
            $genericTypes
        );

        if ($phpDocInfo->getReturnTagValue() instanceof ReturnTagValueNode) {
            $phpDocInfo->getReturnTagValue()->type = $genericTypeNode;
        } else {
            $phpDocInfo->addTagValueNode(new ReturnTagValueNode($genericTypeNode, ''));
        }

        $this->docBlockUpdater->updateRefactoredNodeWithPhpDocInfo($node);

        return $node;
    }

    private function shouldSkipMethod(ClassMethod $classMethod, Scope $scope): bool
    {
        if ($classMethod->stmts === null) {
            return true;
        }

        $classReflection = $scope->getClassReflection();

        if (! $classReflection instanceof ClassReflection || $classReflection->isAnonymous()) {
            return true;
        }

        return ! $classReflection->isTrait()
            && ! $classReflection->isSubclassOfClass($this->reflectionProvider->getClass('Illuminate\Database\Eloquent\Model'));
    }

    private function getRelationshipMethodCall(ClassMethod $classMethod): ?MethodCall
    {
        $returnNode = $this->betterNodeFinder->findFirstInFunctionLikeScoped(
            $classMethod,
            fn (Node $subNode): bool => $subNode instanceof Return_
        );

        if (! $returnNode instanceof Return_) {
            return null;
        }

        $methodCall = $this->betterNodeFinder->findFirstInstanceOf($returnNode, MethodCall::class);

        if (! $methodCall instanceof MethodCall) {
            return null;
        }

        while ($methodCall->var instanceof MethodCall) {
            $methodCall = $methodCall->var;
        }

        $methodName = $this->getName($methodCall->name);
        if ($methodName === null || ! isset(self::RELATIONSHIP_METHODS[$methodName])) {
            return null;
        }

        if ($methodName === 'morphTo') {
            return $methodCall;
        }

        if (count($methodCall->getArgs()) < 1) {
            return null;
        }

        return $methodCall;
    }

    private function getRelatedModelClass(MethodCall $methodCall): ?string
    {
        $args = $methodCall->getArgs();

        $methodName = $this->getName($methodCall->name);
        if ($methodName === 'morphTo') {
            return '\Illuminate\Database\Eloquent\Model';
        }

        if (count($args) === 0) {
            return null;
        }

        $argType = $this->getType($args[0]->value);
        $objectClassNames = $argType->getClassStringObjectType()->getObjectClassNames();

        if ($objectClassNames === []) {
            return null;
        }

        return $objectClassNames[0];
    }

    private function hasValidGenericAnnotation(
        \Rector\BetterPhpDocParser\PhpDocInfo\PhpDocInfo $phpDocInfo,
        string $relationshipMethod,
        string $relatedModel,
        ClassMethod $classMethod
    ): bool {
        $returnTag = $phpDocInfo->getReturnTagValue();
        if (! $returnTag instanceof ReturnTagValueNode) {
            return false;
        }

        if (! $returnTag->type instanceof GenericTypeNode) {
            return false;
        }

        $phpDocPHPStanType = $this->staticTypeMapper->mapPHPStanPhpDocTypeNodeToPHPStanType(
            $returnTag->type,
            $classMethod
        );

        if (! $phpDocPHPStanType instanceof GenericObjectType) {
            return false;
        }

        $phpDocTypes = $phpDocPHPStanType->getTypes();
        if ($phpDocTypes === []) {
            return false;
        }

        if (! $this->typeComparator->areTypesEqual($phpDocTypes[0], new ObjectType($relatedModel))) {
            return false;
        }

        $config = self::RELATIONSHIP_METHODS[$relationshipMethod];

        $expectedThisIndex = isset($config['intermediate']) ? 2 : 1;

        if (count($phpDocTypes) <= $expectedThisIndex) {
            return false;
        }

        if (! $phpDocTypes[$expectedThisIndex] instanceof ThisType) {
            return false;
        }

        return true;
    }

    /**
     * @return IdentifierTypeNode[]
     */
    private function buildGenericTypes(MethodCall $methodCall, string $relationshipMethod, string $relatedModel): array
    {
        $config = self::RELATIONSHIP_METHODS[$relationshipMethod];
        $generics = [];

        $generics[] = new FullyQualifiedIdentifierTypeNode($relatedModel);

        if (isset($config['intermediate'])) {
            $intermediateModel = $this->getIntermediateModelClass($methodCall, $config['intermediate']);
            if ($intermediateModel !== null) {
                $generics[] = new FullyQualifiedIdentifierTypeNode($intermediateModel);
            }
        }

        $generics[] = new IdentifierTypeNode('$this');

        if (isset($config['pivot'])) {
            $generics[] = new FullyQualifiedIdentifierTypeNode($config['pivot']);
        }

        return $generics;
    }

    private function getIntermediateModelClass(MethodCall $methodCall, int $argIndex): ?string
    {
        $args = $methodCall->getArgs();
        if (count($args) <= $argIndex) {
            return null;
        }

        $argType = $this->getType($args[$argIndex]->value);
        $objectClassNames = $argType->getClassStringObjectType()->getObjectClassNames();

        if ($objectClassNames === []) {
            return null;
        }

        return $objectClassNames[0];
    }
}
