<?php

declare(strict_types=1);

namespace RectorLaravel\Rector\ClassMethod;

use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr\ArrayDimFetch;
use PhpParser\Node\Expr\Assign;
use PhpParser\Node\Expr\Closure;
use PhpParser\Node\Expr\PropertyFetch;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name\FullyQualified;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassLike;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Return_;
use PHPStan\Type\ObjectType;
use Rector\Core\Rector\AbstractRector;
use Rector\NodeTypeResolver\Node\AttributeKey;
use Symplify\RuleDocGenerator\Exception\PoorDocumentationException;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/** @see \RectorLaravel\Tests\Rector\ClassMethod\MigrateToSimplifiedAttributeRector\MigrateToSimplifiedAttributeRectorTest */
final class MigrateToSimplifiedAttributeRector extends AbstractRector
{
    /**
     * @return array<class-string<Node>>
     */
    public function getNodeTypes(): array
    {
        return [ClassMethod::class];
    }

    /**
     * @param ClassMethod $node
     */
    public function refactor(Node $node): ?Node
    {
        if ($this->shouldSkipNode($node)) {
            return null;
        }

        $parent = $node->getAttribute(AttributeKey::PARENT_NODE);

        $name = $node->name->name;

        if (!$this->isAccessor($name) && !$this->isMutator($name)) {
            return null;
        }

        $newName = substr($name, 3);
        $newName = substr($newName, 0, -strlen('Attribute'));
        $newName = lcfirst($newName);

        if (empty($newName)) {
            return null;
        }

        // Skip if the new name is already used
        /** @var ClassMethod $method */
        foreach ($parent->getMethods() as $method) {
            if ($this->isName($method, $newName)) {
                return null;
            }
        }

        if ($this->isAccessor($name)) {
            $newMethod = $this->refactorAccessor($newName, $node);
        } else {
            $newMethod = $this->refactorMutator($newName, $node);
        }

        if (!$newMethod) {
            return null;
        }

        // Preserve docblock
        $docComment = $node->getDocComment();

        if ($docComment) {
            $newMethod->setDocComment($docComment);
        }

        return $newMethod;
    }

    /**
     * @throws PoorDocumentationException
     */
    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(
            'Migrates the accessors to the new syntax.', []
        );
    }

    private function shouldSkipNode(ClassMethod $classMethod): bool
    {
        $classLike = $this->betterNodeFinder->findParentType($classMethod, ClassLike::class);

        if (!$classLike instanceof ClassLike) {
            return true;
        }

        if ($classLike instanceof Class_) {
            return !$this->isObjectType($classLike, new ObjectType('Illuminate\Database\Eloquent\Model'));
        }

        return false;
    }

    private function refactorAccessor(string $newName, ClassMethod $node): ClassMethod
    {
        return new ClassMethod(new Identifier($newName), [
            'attrGroups' => $node->attrGroups,
            'flags' => Class_::MODIFIER_PROTECTED,
            'params' => [],
            'returnType' => new FullyQualified('Illuminate\\Database\\Eloquent\\Casts\\Attribute'),
            'stmts' => [
                new Return_(
                    new StaticCall(
                        new FullyQualified('Illuminate\\Database\\Eloquent\\Casts\\Attribute'),
                        new Identifier('make'), [
                            new Arg(
                                new Closure(['params' => $node->params, 'stmts' => $node->stmts]),
                                false,
                                false,
                                [],
                                new Identifier('get')
                            )
                        ]
                    )
                )
            ]
        ]);
    }

    private function refactorMutator(string $newName, ClassMethod $node): ClassMethod|null
    {
        // todo the correct assignment may not be the last one
        $assignment = $this->betterNodeFinder->findLastInstanceOf($node->stmts, Assign::class);

        if (!$assignment) {
            return null;
        }

        /** @var ArrayDimFetch $arrayDimFetch */
        $arrayDimFetch = $assignment->var;

        if (!$arrayDimFetch instanceof ArrayDimFetch) {
            return null;
        }

        $nameToSnakeCase = mb_strtolower(preg_replace('/(.)(?=[A-Z])/u', '$1_', $newName));

        if ($arrayDimFetch->dim->value !== $nameToSnakeCase) {
            return null;
        }

        /** @var PropertyFetch $arrayDimFetch */
        $propertyFetch = $arrayDimFetch->var;

        if (!$propertyFetch instanceof PropertyFetch) {
            return null;
        }

        if (!$this->isName($propertyFetch, 'attributes')) {
            return null;
        }

        if (!$this->isName($propertyFetch->var, 'this')) {
            return null;
        }

        $statements = [
            new Return_($assignment->expr)
        ];

        return new ClassMethod(new Identifier($newName), [
            'attrGroups' => $node->attrGroups,
            'flags' => Class_::MODIFIER_PROTECTED,
            'params' => [],
            'returnType' => new FullyQualified('Illuminate\\Database\\Eloquent\\Casts\\Attribute'),
            'stmts' => [
                new Return_(
                    new StaticCall(
                        new FullyQualified('Illuminate\\Database\\Eloquent\\Casts\\Attribute'),
                        new Identifier('make'), [
                            new Arg(
                                new Closure(['params' => $node->params, 'stmts' => $statements]),
                                false,
                                false,
                                [],
                                new Identifier('set')
                            )
                        ]
                    )
                )
            ]
        ]);
    }

    private function isAccessor(string $name): bool
    {
        return str_starts_with($name, 'get') && str_ends_with($name, 'Attribute');
    }

    private function isMutator(string $name): bool
    {
        return str_starts_with($name, 'set') && str_ends_with($name, 'Attribute');
    }
}
