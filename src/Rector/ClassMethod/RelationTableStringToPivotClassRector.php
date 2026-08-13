<?php

declare(strict_types=1);

namespace RectorLaravel\Rector\ClassMethod;

use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr\ClassConstFetch;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Name\FullyQualified;
use PhpParser\Node\Scalar\String_;
use PhpParser\Node\Stmt\ClassMethod;
use PHPStan\Reflection\ClassReflection;
use PHPStan\Reflection\ReflectionProvider;
use PHPStan\Type\ObjectType;
use Rector\PhpParser\Node\BetterNodeFinder;
use Rector\PHPStan\ScopeFetcher;
use Rector\VersionBonding\Contract\ComposerPackageConstraintInterface;
use Rector\VersionBonding\ValueObject\ComposerPackageConstraint;
use RectorLaravel\AbstractRector;
use RectorLaravel\NodeAnalyzer\PivotClassAnalyzer;
use RectorLaravel\Tests\Rector\ClassMethod\RelationTableStringToPivotClassRector\RelationTableStringToPivotClassRectorTest;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see RelationTableStringToPivotClassRectorTest
 */
final class RelationTableStringToPivotClassRector extends AbstractRector implements ComposerPackageConstraintInterface
{
    public function __construct(
        private readonly BetterNodeFinder $betterNodeFinder,
        private readonly PivotClassAnalyzer $pivotClassAnalyzer,
        private readonly ReflectionProvider $reflectionProvider,
    ) {}

    public function provideComposerPackageConstraint(): ComposerPackageConstraint
    {
        return new ComposerPackageConstraint('laravel/framework', '>=6.0');
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(
            'Changes the pivot table name of a many to many relation to the pivot model class. The pivot model is taken from a using() call or the relation generics when present, otherwise it is looked for alongside the related and declaring models, and is only taken for a pivot when it is one or the table is the one the framework would have built.',
            [
                new CodeSample(
                    <<<'CODE_SAMPLE'
$this->belongsToMany(Tag::class, 'post_tag');
CODE_SAMPLE
                    ,
                    <<<'CODE_SAMPLE'
$this->belongsToMany(Tag::class, \App\Models\PostTag::class);
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
    public function refactor(Node $node): ?ClassMethod
    {
        if ($this->shouldSkip($node)) {
            return null;
        }

        $hasChanged = false;

        foreach ($this->betterNodeFinder->findInstancesOfInFunctionLikeScoped($node, MethodCall::class) as $methodCall) {
            $tableArgument = $this->pivotClassAnalyzer->matchRelationTableArg($methodCall);

            if (! $tableArgument instanceof Arg || ! $tableArgument->value instanceof String_) {
                continue;
            }

            $pivotClass = $this->pivotClassAnalyzer->resolvePivotClass($node, $methodCall, $tableArgument->value->value);

            if ($pivotClass === null) {
                continue;
            }

            $tableArgument->value = new ClassConstFetch(new FullyQualified($pivotClass), 'class');
            $hasChanged = true;
        }

        return $hasChanged ? $node : null;
    }

    private function shouldSkip(ClassMethod $classMethod): bool
    {
        if ($classMethod->stmts === null || $classMethod->isStatic()) {
            return true;
        }

        $classReflection = ScopeFetcher::fetch($classMethod)->getClassReflection();

        if (! $classReflection instanceof ClassReflection || $classReflection->isAnonymous()) {
            return true;
        }

        if (
            ! $classReflection->isTrait()
            && ! $classReflection->isSubclassOfClass($this->reflectionProvider->getClass('Illuminate\Database\Eloquent\Model'))
        ) {
            return true;
        }

        $returnType = $classMethod->getReturnType();

        if ($returnType === null) {
            return false;
        }

        $declaredType = $this->nodeTypeResolver->getType($returnType);

        $objectType = new ObjectType('Illuminate\Database\Eloquent\Relations\BelongsToMany');

        // a wider type such as Relation is kept as well, only types which cannot be a many to many relation are skipped
        return $declaredType->isSuperTypeOf($objectType)->no()
            && $objectType->isSuperTypeOf($declaredType)->no();
    }
}
