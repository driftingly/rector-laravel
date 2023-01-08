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

        $parentClass = $node->getAttribute(AttributeKey::PARENT_NODE);

        $nodeName = $node->name->name;

        if (!$this->isAccessor($nodeName) && !$this->isMutator($nodeName)) {
            return null;
        }

        $attributeName = $this->parseAttributeName($nodeName);

        if (empty($attributeName)) {
            return null;
        }

        // Skip if the new attribute name is already used
        /** @var ClassMethod $method */
        foreach ($parentClass->getMethods() as $method) {
            if ($this->isName($method, $attributeName)) {
                return null;
            }
        }

        if ($this->isAccessor($nodeName)) {
            $mutator = $this->findPossibleMutator($parentClass, $attributeName);
            $accessor = $node;
        } else {
            $accessor = $this->findPossibleAccessor($parentClass, $attributeName);
            $mutator = $node;
        }

        // This means we have both an accessor and a mutator
        // So we generate the new method where the accessor
        // is placed on the model and remove the mutator,
        // so we don't run the refactoring twice
        if ($accessor instanceof ClassMethod && $mutator instanceof ClassMethod && $this->isMutator($nodeName)) {
            $this->removeNode($mutator);
            return null;
        }

        if ($accessor instanceof ClassMethod && $mutator instanceof ClassMethod) {
            $newNode = $this->createAccessorAndMutator($accessor, $mutator, $attributeName);
        } else if ($accessor instanceof ClassMethod) {
            $newNode = $this->createAccessor($attributeName, $node);
        } else {
            $newNode = $this->createMutator($attributeName, $node);
        }

        // Preserve docblock
        $docComment = $node->getDocComment();

        if ($docComment) {
            $newNode->setDocComment($docComment);
        }

        return $newNode;
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

    private function createAccessor(string $attributeName, ClassMethod $node): ClassMethod
    {
        return $this->createAttributeClassMethod($attributeName, $node, $node->stmts, 'get');
    }

    private function createMutator(string $attributeName, ClassMethod $node): ClassMethod
    {
        $statements = $this->getMutatorStatements($node);

        return $this->createAttributeClassMethod($attributeName, $node, $statements, 'set');
    }

    private function createAccessorAndMutator(ClassMethod $accessor, ClassMethod $mutator, string $attributeName): ClassMethod
    {
        return new ClassMethod(new Identifier($attributeName), [
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

    private function createAttributeClassMethod(string $attributeName, ClassMethod $node, array $statements, string $identifierName): ClassMethod
    {
        return new ClassMethod(new Identifier($attributeName), [
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

    private function isAccessor(string $nodeName): bool
    {
        return str_starts_with($nodeName, 'get') && str_ends_with($nodeName, 'Attribute');
    }

    private function isMutator(string $nodeName): bool
    {
        return str_starts_with($nodeName, 'set') && str_ends_with($nodeName, 'Attribute');
    }

    private function findPossibleAccessor(mixed $parent, $attributeName): ?ClassMethod
    {
        /** @var ClassMethod $method */
        foreach ($parent->getMethods() as $method) {
            if ($method->name->toString() === "get" . ucfirst($attributeName) . "Attribute") {
                return $method;
            }
        }

        return null;
    }

    private function findPossibleMutator(mixed $parent, $attributeName): ?ClassMethod
    {
        /** @var ClassMethod $method */
        foreach ($parent->getMethods() as $method) {
            if ($method->name->toString() === "set" . ucfirst($attributeName) . "Attribute") {
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

        // Get all statements that are assignments to attributes
        // updated for the new syntax
        $attributesAssignmentStatements = [];
        /** @var Assign $assignment */
        foreach ($assignments as $assignment) {
            if (!$this->isAttributesAssignment($assignment)) {
                continue;
            }

            $attributesAssignmentStatements[] = new ArrayItem($assignment->expr, $assignment->var->dim);
        }

        // Get all statements that are not assignments to attributes
        $statements = array_filter($node->stmts, function (Stmt $statement) use ($assignments) {
            if (!$statement->expr instanceof Assign) {
                return true;
            }

            return !$this->isAttributesAssignment($statement->expr);
        });

        // Append the updated attributes assignment statements
        $statements[] = new Return_(new Array_($attributesAssignmentStatements));

        return $statements;
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

    private function parseAttributeName(string $nodeName): string
    {
        $attributeName = substr($nodeName, 3);
        $attributeName = substr($attributeName, 0, -strlen('Attribute'));
        return lcfirst($attributeName);
    }
}
