<?php

declare(strict_types=1);

namespace RectorLaravel\Rector\ClassMethod;

use PhpParser\Node;
use PhpParser\Node\Stmt\ClassMethod;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\ClassReflection;
use PHPStan\Type\ObjectType;
use Rector\Php80\NodeAnalyzer\PhpAttributeAnalyzer;
use Rector\Php81\NodeManipulator\AttributeGroupNewLiner;
use Rector\PHPStan\ScopeFetcher;
use Rector\Privatization\NodeManipulator\VisibilityManipulator;
use RectorLaravel\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \RectorLaravel\Tests\Rector\ClassMethod\MakeModelAttributesAndScopesProtectedRector\MakeModelAttributesAndScopesProtectedRectorTest
 */
class MakeModelAttributesAndScopesProtectedRector extends AbstractRector
{
    /**
     * @readonly
     */
    private VisibilityManipulator $visibilityManipulator;
    /**
     * @readonly
     */
    private PhpAttributeAnalyzer $phpAttributeAnalyzer;
    /**
     * @readonly
     */
    private AttributeGroupNewLiner $attributeGroupNewLiner;
    public function __construct(VisibilityManipulator $visibilityManipulator, PhpAttributeAnalyzer $phpAttributeAnalyzer, AttributeGroupNewLiner $attributeGroupNewLiner)
    {
        $this->visibilityManipulator = $visibilityManipulator;
        $this->phpAttributeAnalyzer = $phpAttributeAnalyzer;
        $this->attributeGroupNewLiner = $attributeGroupNewLiner;
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(
            'Makes Model attributes and scopes protected',
            [
                new CodeSample(
                    <<<'CODE_SAMPLE'
class User extends Model
{
    public function foo(): Attribute
    {
        return Attribute::get(fn () => $this->bar);
    }

    #[Scope]
    public function active(Builder $query): Builder
    {
        return $query->where('active', true);
    }
}
CODE_SAMPLE

                    ,
                    <<<'CODE_SAMPLE'
class User extends Model
{
    protected function foo(): Attribute
    {
        return Attribute::get(fn () => $this->bar);
    }

    #[Scope]
    protected function active(Builder $query): Builder
    {
        return $query->where('active', true);
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

    /** @param  ClassMethod  $node */
    public function refactor(Node $node): ?Node
    {
        $scope = ScopeFetcher::fetch($node);

        if ($this->shouldSkipNode($node, $scope)) {
            return null;
        }

        $this->visibilityManipulator->makeProtected($node);

        if ($node->attrGroups !== []) {
            $this->attributeGroupNewLiner->newLine($this->file, $node);
        }

        return $node;
    }

    private function shouldSkipNode(ClassMethod $classMethod, Scope $scope): bool
    {
        if ($classMethod->isProtected() || $classMethod->isStatic()) {
            return true;
        }

        if (! $this->isAttributeMethod($classMethod) && ! $this->isScopeMethod($classMethod)) {
            return true;
        }

        $classReflection = $scope->getClassReflection();

        if (! $classReflection instanceof ClassReflection || $classReflection->isAnonymous()) {
            return true;
        }

        if (
            $classReflection->getParentClass() instanceof ClassReflection &&
            $classReflection->getParentClass()->hasMethod($this->getName($classMethod)) &&
            $classReflection->getParentClass()->getMethod($this->getName($classMethod), $scope)->isPublic()
        ) {
            return true;
        }

        return ! $classReflection->isTrait()
            && ! $classReflection->is('Illuminate\Database\Eloquent\Model');
    }

    private function isAttributeMethod(ClassMethod $classMethod): bool
    {
        $name = $this->getName($classMethod);

        if ((bool) preg_match('/^[gs]et.+Attribute$/', $name)) {
            return true;
        }

        if (! $classMethod->returnType instanceof Node) {
            return false;
        }

        return $this->isObjectType($classMethod->returnType, new ObjectType('Illuminate\Database\Eloquent\Casts\Attribute'));
    }

    private function isScopeMethod(ClassMethod $classMethod): bool
    {
        $name = $this->getName($classMethod);

        if ((bool) preg_match('/^scope.+$/', $name)) {
            return true;
        }

        return $this->phpAttributeAnalyzer->hasPhpAttribute($classMethod, 'Illuminate\Database\Eloquent\Attributes\Scope');
    }
}
