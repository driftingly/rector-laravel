<?php

declare(strict_types=1);

namespace RectorLaravel\Rector\ClassMethod;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr\ClassConstFetch;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Name\FullyQualified;
use PhpParser\Node\Scalar\String_;
use PhpParser\Node\Stmt\ClassMethod;
use PHPStan\Reflection\ClassReflection;
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
    ) {}

    public function provideComposerPackageConstraint(): ComposerPackageConstraint
    {
        return new ComposerPackageConstraint('laravel/framework', '>=8.0');
    }

    public function getRuleDefinition(): RuleDefinition
    {
        $description = "Changes the pivot table name of a many to many relation to the pivot model class.\n\n"
            . 'This is not purely a naming change: the framework applies a pivot class given as the table '
            . "as if `using()` had been called, so the relation's pivot instances gain the class's casts, "
            . 'accessors, `$timestamps` and `$incrementing`.';

        return new RuleDefinition(
            $description,
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
        if ($classMethod->stmts === null) {
            return true;
        }

        $returnType = $classMethod->getReturnType();

        if ($returnType !== null && $this->shouldSkipReturnType($returnType)) {
            return true;
        }

        $classReflection = ScopeFetcher::fetch($classMethod)->getClassReflection();

        // an anonymous class has no name to look for a pivot alongside, nor one to build a joining table from
        return ! $classReflection instanceof ClassReflection
            || $classReflection->isAnonymous()
            || (! $classReflection->isTrait() && ! $classReflection->is(Model::class));
    }

    private function shouldSkipReturnType(Node $returnType): bool
    {
        $declaredType = $this->nodeTypeResolver->getType($returnType);

        $objectType = new ObjectType(BelongsToMany::class);

        // a wider type such as Relation is kept as well, only types which cannot be a many to many relation are skipped
        return $declaredType->isSuperTypeOf($objectType)->no()
            && $objectType->isSuperTypeOf($declaredType)->no();
    }
}
