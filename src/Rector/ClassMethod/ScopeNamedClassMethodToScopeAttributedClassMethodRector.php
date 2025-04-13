<?php

declare(strict_types=1);

namespace RectorLaravel\Rector\ClassMethod;

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
    /**
     * @readonly
     */
    private PhpAttributeAnalyzer $phpAttributeAnalyzer;
    /**
     * @readonly
     */
    private ReflectionProvider $reflectionProvider;
    /**
     * @var string
     */
    private const SCOPE_ATTRIBUTE = 'Illuminate\Database\Eloquent\Attributes\Scope';

    public function __construct(PhpAttributeAnalyzer $phpAttributeAnalyzer, ReflectionProvider $reflectionProvider)
    {
        $this->phpAttributeAnalyzer = $phpAttributeAnalyzer;
        $this->reflectionProvider = $reflectionProvider;
    }

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
    public function active($query)
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
            $name = $this->getName($classMethod);
            // make sure it starts with scope and the next character is upper case
            if (strncmp($name, 'scope', strlen('scope')) !== 0 || ! ctype_upper(substr($name, 5, 1))) {
                continue;
            }

            $newName = lcfirst(str_replace('scope', '', $name));

            if ($classReflection->hasMethod($newName)) {
                continue;
            }

            if ($this->phpAttributeAnalyzer->hasPhpAttribute($classMethod, self::SCOPE_ATTRIBUTE)) {
                continue;
            }

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
