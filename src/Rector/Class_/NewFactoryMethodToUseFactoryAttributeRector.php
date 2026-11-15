<?php

declare(strict_types=1);

namespace RectorLaravel\Rector\Class_;

use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Attribute;
use PhpParser\Node\AttributeGroup;
use PhpParser\Node\Expr\ClassConstFetch;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Name;
use PhpParser\Node\Name\FullyQualified;
use PhpParser\Node\Stmt;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Property;
use PHPStan\Reflection\ClassReflection;
use PHPStan\Type\ObjectType;
use Rector\Php80\NodeAnalyzer\PhpAttributeAnalyzer;
use Rector\Reflection\ReflectionResolver;
use Rector\RuleDoc\CodeSample\CodeSample;
use Rector\RuleDoc\RuleDefinition;
use Rector\TypeDeclarationDocblocks\NodeFinder\ReturnNodeFinder;
use Rector\VersionBonding\Contract\ComposerPackageConstraintInterface;
use Rector\VersionBonding\ValueObject\ComposerPackageConstraint;
use RectorLaravel\AbstractRector;
use RectorLaravel\Tests\Rector\Class_\NewFactoryMethodToUseFactoryAttributeRector\NewFactoryMethodToUseFactoryAttributeRectorTest;

/**
 * @see NewFactoryMethodToUseFactoryAttributeRectorTest
 */
final class NewFactoryMethodToUseFactoryAttributeRector extends AbstractRector implements ComposerPackageConstraintInterface
{
    private const string USE_FACTORY_ATTRIBUTE = 'Illuminate\Database\Eloquent\Attributes\UseFactory';

    private const string HAS_FACTORY_TRAIT = 'Illuminate\Database\Eloquent\Factories\HasFactory';

    public function __construct(
        private readonly PhpAttributeAnalyzer $phpAttributeAnalyzer,
        private readonly ReflectionResolver $reflectionResolver,
        private readonly ReturnNodeFinder $returnNodeFinder,
    ) {}

    public function provideComposerPackageConstraint(): ComposerPackageConstraint
    {
        return new ComposerPackageConstraint('laravel/framework', '>=12.4');
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(
            'Changes model newFactory method or $factory property to use the UseFactory attribute',
            [new CodeSample(
                <<<'CODE_SAMPLE'
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    use HasFactory;

    protected static function newFactory(): UserFactory
    {
        return UserFactory::new();
    }
}
CODE_SAMPLE,
                <<<'CODE_SAMPLE'
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[UseFactory(UserFactory::class)]
class User extends Model
{
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
        if ($this->shouldSkip($node)) {
            return null;
        }

        $newFactoryMethod = $node->getMethod('newFactory');
        $factoryProperty = $node->getProperty('factory');

        if ($newFactoryMethod instanceof ClassMethod && $factoryProperty instanceof Property) {
            return null;
        }

        if ($newFactoryMethod instanceof ClassMethod) {
            $replacedStmt = $newFactoryMethod;
            $factoryClass = $this->resolveFactoryClassFromMethod($newFactoryMethod);
        } elseif ($factoryProperty instanceof Property) {
            $replacedStmt = $factoryProperty;
            $factoryClass = $this->resolveFactoryClassFromProperty($factoryProperty);
        } else {
            return null;
        }

        if (! $factoryClass instanceof ClassConstFetch) {
            return null;
        }

        $node->attrGroups[] = new AttributeGroup([
            new Attribute(
                new FullyQualified(self::USE_FACTORY_ATTRIBUTE),
                [new Arg($factoryClass)]
            ),
        ]);

        $this->removeStmt($node, $replacedStmt);

        return $node;
    }

    private function shouldSkip(Class_ $class): bool
    {
        if (! $this->isObjectType($class, new ObjectType('Illuminate\Database\Eloquent\Model'))) {
            return true;
        }

        // The attribute is only read from the model's own class, so subclasses would lose an inherited factory
        if ($class->isAbstract()) {
            return true;
        }

        if ($this->phpAttributeAnalyzer->hasPhpAttribute($class, self::USE_FACTORY_ATTRIBUTE)) {
            return true;
        }

        $classReflection = $this->reflectionResolver->resolveClassReflection($class);

        return ! $classReflection instanceof ClassReflection
            || ! $classReflection->hasTraitUse(self::HAS_FACTORY_TRAIT);
    }

    private function resolveFactoryClassFromMethod(ClassMethod $classMethod): ?ClassConstFetch
    {
        if (! $classMethod->isStatic() || $classMethod->stmts === null || count($classMethod->stmts) !== 1) {
            return null;
        }

        $return = $this->returnNodeFinder->findOnlyReturnWithExpr($classMethod);
        if (! $return?->expr instanceof StaticCall) {
            return null;
        }

        $staticCall = $return->expr;
        if (! $this->isName($staticCall->name, 'new') || $staticCall->args !== []) {
            return null;
        }

        if (! $staticCall->class instanceof Name || $this->isNames($staticCall->class, ['self', 'static', 'parent'])) {
            return null;
        }

        return new ClassConstFetch($staticCall->class, 'class');
    }

    private function resolveFactoryClassFromProperty(Property $property): ?ClassConstFetch
    {
        if (! $property->isStatic() || count($property->props) !== 1) {
            return null;
        }

        $default = $property->props[0]->default;
        if (! $default instanceof ClassConstFetch || ! $this->isName($default->name, 'class')) {
            return null;
        }

        if (! $default->class instanceof Name || $this->isNames($default->class, ['self', 'static', 'parent'])) {
            return null;
        }

        return $default;
    }

    private function removeStmt(Class_ $class, Stmt $replacedStmt): void
    {
        foreach ($class->stmts as $key => $stmt) {
            if ($stmt === $replacedStmt) {
                unset($class->stmts[$key]);

                return;
            }
        }
    }
}
