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
use PhpParser\Node\Stmt\Expression;
use PhpParser\Node\Stmt\Return_;
use PHPStan\Type\ObjectType;
use Rector\Core\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
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

        $nodeName = $node->name->name;

        if (! $this->isAccessor($nodeName) && ! $this->isMutator($nodeName)) {
            return null;
        }

        $attributeName = $this->parseAttributeName($nodeName);

        if ($attributeName === '') {
            return null;
        }

        /** @var ClassLike $parentClass */
        $parentClass = $this->betterNodeFinder->findParentType($node, ClassLike::class);

        // Skip if the new attribute name is already used
        foreach ($parentClass->getMethods() as $classMethod) {
            if ($this->isName($classMethod, $attributeName)) {
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
        } elseif ($accessor instanceof ClassMethod) {
            $newNode = $this->createAccessor($attributeName, $node);
        } else {
            $newNode = $this->createMutator($attributeName, $node);
        }

        // Preserve docblock
        $docComment = $node->getDocComment();

        if ($docComment !== null) {
            $newNode->setDocComment($docComment);
        }

        return $newNode;
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Migrate to the new Model attributes syntax', [
            new CodeSample(
                <<<'CODE_SAMPLE'
use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    public function getFirstNameAttribute($value)
    {
        return ucfirst($value);
    }

    public function setFirstNameAttribute($value)
    {
        $this->attributes['first_name'] = strtolower($value);
        $this->attributes['first_name_upper'] = strtoupper($value);
    }
}
CODE_SAMPLE
                ,
                <<<'CODE_SAMPLE'
use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    protected function firstName(): \Illuminate\Database\Eloquent\Casts\Attribute
    {
        return \Illuminate\Database\Eloquent\Casts\Attribute::make(get: function ($value) {
            return ucfirst($value);
        }, set: function ($value) {
            return ['first_name' => strtolower($value), 'first_name_upper' => strtoupper($value)];
        });
    }
}
CODE_SAMPLE
            ),
        ]);
    }

    private function shouldSkipNode(ClassMethod $classMethod): bool
    {
        $classLike = $this->betterNodeFinder->findParentType($classMethod, ClassLike::class);

        if (! $classLike instanceof ClassLike) {
            return true;
        }

        if ($classLike instanceof Class_) {
            return ! $this->isObjectType($classLike, new ObjectType('Illuminate\Database\Eloquent\Model'));
        }

        return false;
    }

    private function createAccessor(string $attributeName, ClassMethod $classMethod): ClassMethod
    {
        return $this->createAttributeClassMethod($attributeName, $classMethod, $classMethod->stmts, 'get');
    }

    private function createMutator(string $attributeName, ClassMethod $classMethod): ClassMethod
    {
        $statements = $this->getMutatorStatements($classMethod);

        return $this->createAttributeClassMethod($attributeName, $classMethod, $statements, 'set');
    }

    private function createAccessorAndMutator(
        ClassMethod $accessor,
        ClassMethod $mutator,
        string $attributeName
    ): ClassMethod {
        return new ClassMethod(new Identifier($attributeName), [
            'attrGroups' => [],
            'flags' => Class_::MODIFIER_PROTECTED,
            'params' => [],
            'returnType' => new FullyQualified('Illuminate\\Database\\Eloquent\\Casts\\Attribute'),
            'stmts' => [
                new Return_(
                    new StaticCall(
                        new FullyQualified('Illuminate\\Database\\Eloquent\\Casts\\Attribute'),
                        new Identifier('make'),
                        [
                            new Arg(
                                new Closure([
                                    'params' => $accessor->params,
                                    'stmts' => $accessor->stmts,
                                ]),
                                false,
                                false,
                                [],
                                new Identifier('get')
                            ),
                            new Arg(
                                new Closure([
                                    'params' => $mutator->params,
                                    'stmts' => $this->getMutatorStatements($mutator),
                                ]),
                                false,
                                false,
                                [],
                                new Identifier('set')
                            ),
                        ]
                    )
                ),
            ],
        ]);
    }

    /**
     * @param array<Stmt>|null $statements
     */
    private function createAttributeClassMethod(
        string $attributeName,
        ClassMethod $classMethod,
        ?array $statements,
        string $identifierName
    ): ClassMethod {
        return new ClassMethod(new Identifier($attributeName), [
            'attrGroups' => $classMethod->attrGroups,
            'flags' => Class_::MODIFIER_PROTECTED,
            'params' => [],
            'returnType' => new FullyQualified('Illuminate\\Database\\Eloquent\\Casts\\Attribute'),
            'stmts' => [
                new Return_(
                    new StaticCall(
                        new FullyQualified('Illuminate\\Database\\Eloquent\\Casts\\Attribute'),
                        new Identifier('make'),
                        [
                            new Arg(
                                new Closure([
                                    'params' => $classMethod->params,
                                    'stmts' => $statements,
                                ]),
                                false,
                                false,
                                [],
                                new Identifier($identifierName)
                            ),
                        ]
                    )
                ),
            ],
        ]);
    }

    private function isAccessor(string $nodeName): bool
    {
        return strncmp($nodeName, 'get', strlen('get')) === 0 && substr_compare(
            $nodeName,
            'Attribute',
            -strlen('Attribute')
        ) === 0;
    }

    private function isMutator(string $nodeName): bool
    {
        return strncmp($nodeName, 'set', strlen('set')) === 0 && substr_compare(
            $nodeName,
            'Attribute',
            -strlen('Attribute')
        ) === 0;
    }

    private function findPossibleAccessor(ClassLike $classLike, string $attributeName): ?ClassMethod
    {
        foreach ($classLike->getMethods() as $classMethod) {
            if ($classMethod->name->toString() === 'get' . ucfirst($attributeName) . 'Attribute') {
                return $classMethod;
            }
        }

        return null;
    }

    private function findPossibleMutator(ClassLike $classLike, string $attributeName): ?ClassMethod
    {
        foreach ($classLike->getMethods() as $classMethod) {
            if ($classMethod->name->toString() === 'set' . ucfirst($attributeName) . 'Attribute') {
                return $classMethod;
            }
        }

        return null;
    }

    /**
     * @return Stmt[]
     */
    private function getMutatorStatements(ClassMethod $classMethod): array
    {
        $assignments = $this->betterNodeFinder->findInstancesOf($classMethod->stmts ?? [], [Assign::class]);

        // Get all statements that are assignments to attributes
        // updated for the new syntax
        $attributesAssignmentStatements = [];
        /** @var Assign $assignment */
        foreach ($assignments as $assignment) {
            if (! $this->isAttributesAssignment($assignment)) {
                continue;
            }

            /** @var ArrayDimFetch $assignmentVar */
            $assignmentVar = $assignment->var;

            $attributesAssignmentStatements[] = new ArrayItem($assignment->expr, $assignmentVar->dim);
        }

        // Get all statements that are not assignments to attributes
        $statements = array_filter(
            $classMethod->stmts ?? [],
            function (Stmt $stmt) {
                if (! $stmt instanceof Expression) {
                    return true;
                }

                if (! $stmt->expr instanceof Assign) {
                    return true;
                }

                return ! $this->isAttributesAssignment($stmt->expr);
            }
        );

        // Append the updated attributes assignment statements
        $statements[] = new Return_(new Array_($attributesAssignmentStatements));

        return $statements;
    }

    private function isAttributesAssignment(Assign $assign): bool
    {
        $arrayDimFetch = $assign->var;

        if (! $arrayDimFetch instanceof ArrayDimFetch) {
            return false;
        }

        $propertyFetch = $arrayDimFetch->var;

        if (! $propertyFetch instanceof PropertyFetch) {
            return false;
        }

        if (! $this->isName($propertyFetch, 'attributes')) {
            return false;
        }

        return $this->isName($propertyFetch->var, 'this');
    }

    private function parseAttributeName(string $nodeName): string
    {
        $attributeName = substr($nodeName, 3);
        $attributeName = substr($attributeName, 0, -strlen('Attribute'));
        return lcfirst($attributeName);
    }
}
