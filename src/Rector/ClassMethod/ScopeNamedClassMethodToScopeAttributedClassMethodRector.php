<?php

declare(strict_types=1);

namespace RectorLaravel\Rector\ClassMethod;

use PhpParser\Modifiers;
use PhpParser\Node;
use PhpParser\Node\Attribute;
use PhpParser\Node\AttributeGroup;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name\FullyQualified;
use PhpParser\Node\Stmt\Class_;
use PHPStan\Reflection\ReflectionProvider;
use PHPStan\Type\ObjectType;
use Rector\Php80\NodeAnalyzer\PhpAttributeAnalyzer;
use RectorLaravel\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \RectorLaravel\Tests\Rector\ClassMethod\ScopeNamedClassMethodToScopeAttributedClassMethodRector\ScopeNamedClassMethodToScopeAttributedClassMethodRectorTest
 */
final class ScopeNamedClassMethodToScopeAttributedClassMethodRector extends AbstractRector
{
    private const string SCOPE_ATTRIBUTE = 'Illuminate\Database\Eloquent\Attributes\Scope';

    public function __construct(
        private readonly PhpAttributeAnalyzer $phpAttributeAnalyzer,
        private readonly ReflectionProvider $reflectionProvider,
    ) {}

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(
            'Changes model scope methods to use the scope attribute',
            [new CodeSample(
                <<<'CODE_SAMPLE'
class User extends Model
{
    public function scopeActive($query)
    {
        return $query->where('active', 1);
    }
}
CODE_SAMPLE,
                <<<'CODE_SAMPLE'
class User extends Model
{
    #[\Illuminate\Database\Eloquent\Attributes\Scope]
    protected function active($query)
    {
        return $query->where('active', 1);
    }
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

        if (! is_string($className = $this->getName($node))) {
            return null;
        }

        $classReflection = $this->reflectionProvider->getClass($className);

        $changes = false;
        foreach ($node->getMethods() as $classMethod) {
            if ($classMethod->isAbstract()) {
                continue;
            }

            $name = $this->getName($classMethod);
            // make sure it starts with scope and the next character is upper case
            if (! str_starts_with($name, 'scope') || ! ctype_upper(substr($name, 5, 1))) {
                continue;
            }

            $newName = lcfirst(str_replace('scope', '', $name));

            if ($classReflection->hasMethod($newName)) {
                continue;
            }

            if ($this->phpAttributeAnalyzer->hasPhpAttribute($classMethod, self::SCOPE_ATTRIBUTE)) {
                continue;
            }

            $classMethod->flags = Modifiers::PROTECTED;
            $classMethod->name = new Identifier($newName);
            $classMethod->attrGroups[] = new AttributeGroup([new Attribute(new FullyQualified(self::SCOPE_ATTRIBUTE))]);
            $changes = true;
        }

        if ($changes === false) {
            return null;
        }

        return $node;
    }
}
