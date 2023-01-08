<?php

declare(strict_types=1);

namespace RectorLaravel\Rector\ClassMethod;

use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr\Array_;
use PhpParser\Node\Expr\ArrayDimFetch;
use PhpParser\Node\Expr\ArrayItem;
use PhpParser\Node\Expr\Assign;
use PhpParser\Node\Expr\Closure;
use PhpParser\Node\Expr\PropertyFetch;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name\FullyQualified;
use PhpParser\Node\Stmt;
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
            $mutator = $this->findMutator($parent, $newName);
            $accessor = $node;
        } else {
            $accessor = $this->findAccessor($parent, $newName);
            $mutator = $node;
        }

        // This means we have both an accessor and a mutator
        // So we generate the new method where the accessor is
        // and remove the mutator
        if ($accessor && $mutator && $this->isMutator($name)) {
            $this->removeNode($mutator);
            return null;
        }

        if ($accessor && $mutator) {
            $newMethod = $this->createAccessorAndMutator($accessor, $mutator, $newName);
        } else if ($accessor) {
            $newMethod = $this->createAccessor($newName, $node);
        } else {
            $newMethod = $this->createMutator($newName, $node);
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

    private function createAccessor(string $newName, ClassMethod $node): ClassMethod
    {
        return $this->createSimplifiedAttributeMethod($newName, $node, $node->stmts, 'get');
    }

    private function createMutator(string $newName, ClassMethod $node): ClassMethod
    {
        $statements = $this->getMutatorStatements($node);

        return $this->createSimplifiedAttributeMethod($newName, $node, $statements, 'set');
    }

    private function createAccessorAndMutator(ClassMethod $accessor, ClassMethod $mutator, string $newName): ClassMethod
    {
        return new ClassMethod(new Identifier($newName), [
            'attrGroups' => [],
            'flags' => Class_::MODIFIER_PROTECTED,
            'params' => [],
            'returnType' => new FullyQualified('Illuminate\\Database\\Eloquent\\Casts\\Attribute'),
            'stmts' => [
                new Return_(
                    new StaticCall(
                        new FullyQualified('Illuminate\\Database\\Eloquent\\Casts\\Attribute'),
                        new Identifier('make'), [
                            new Arg(
                                new Closure(['params' => $accessor->params, 'stmts' => $accessor->stmts]),
                                false,
                                false,
                                [],
                                new Identifier('get')
                            ),
                            new Arg(
                                new Closure(['params' => $mutator->params, 'stmts' => $this->getMutatorStatements($mutator)]),
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

    private function createSimplifiedAttributeMethod(string $newName, ClassMethod $node, array $statements, string $identifierName): ClassMethod
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
                                new Closure(['params' => $node->params, 'stmts' => $statements]),
                                false,
                                false,
                                [],
                                new Identifier($identifierName)
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

    private function getAssignmentStatement(Assign $assignment): ?ArrayItem
    {
        /** @var ArrayDimFetch $arrayDimFetch */
        $arrayDimFetch = $assignment->var;

        if (!$this->isAttributesAssignment($assignment)) {
            return null;
        }

        return new ArrayItem($assignment->expr, $arrayDimFetch->dim);
    }

    private function isAttributesAssignment(Assign $assignment): bool
    {
        /** @var ArrayDimFetch $arrayDimFetch */
        $arrayDimFetch = $assignment->var;

        if (!$arrayDimFetch instanceof ArrayDimFetch) {
            return false;
        }

        /** @var PropertyFetch $arrayDimFetch */
        $propertyFetch = $arrayDimFetch->var;

        if (!$propertyFetch instanceof PropertyFetch) {
            return false;
        }

        if (!$this->isName($propertyFetch, 'attributes')) {
            return false;
        }

        if (!$this->isName($propertyFetch->var, 'this')) {
            return false;
        }

        return true;
    }

    private function findMutator(mixed $parent, $newName): ?ClassMethod
    {
        /** @var ClassMethod $method */
        foreach ($parent->getMethods() as $method) {
            if ($method->name->toString() === "set".ucfirst($newName)."Attribute") {
                return $method;
            }
        }

        return null;
    }

    private function findAccessor(mixed $parent, $newName): ?ClassMethod
    {
        /** @var ClassMethod $method */
        foreach ($parent->getMethods() as $method) {
            if ($method->name->toString() === "get".ucfirst($newName)."Attribute") {
                return $method;
            }
        }

        return null;
    }

    /**
     * @param ClassMethod $node
     * @return Stmt[]
     */
    private function getMutatorStatements(ClassMethod $node): array
    {
        $assignments = $this->betterNodeFinder->findInstancesOf($node->stmts, [Assign::class]);

        $assignmentStatements = [];
        /** @var Assign $assignment */
        foreach ($assignments as $assignment) {
            $statement = $this->getAssignmentStatement($assignment);

            if ($statement) {
                $assignmentStatements[] = $statement;
            }
        }

        $statements = array_filter($node->stmts, function (Stmt $statement) use ($assignments) {
            if (!$statement->expr instanceof Assign) {
                return true;
            }

            return !$this->isAttributesAssignment($statement->expr);
        });

        $statements[] = new Return_(new Array_($assignmentStatements));
        return $statements;
    }
}
