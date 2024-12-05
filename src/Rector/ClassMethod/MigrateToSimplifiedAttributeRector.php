<?php

declare(strict_types=1);

namespace RectorLaravel\Rector\ClassMethod;

use PhpParser\Comment\Doc;
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
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Expression;
use PhpParser\Node\Stmt\Return_;
use PhpParser\NodeTraverser;
use PHPStan\Type\ObjectType;
use Rector\PhpParser\Node\BetterNodeFinder;
use Rector\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/** @see \RectorLaravel\Tests\Rector\ClassMethod\MigrateToSimplifiedAttributeRector\MigrateToSimplifiedAttributeRectorTest */
final class MigrateToSimplifiedAttributeRector extends AbstractRector
{
    /**
     * @readonly
     * @var \Rector\PhpParser\Node\BetterNodeFinder
     */
    private $betterNodeFinder;
    public function __construct(BetterNodeFinder $betterNodeFinder)
    {
        $this->betterNodeFinder = $betterNodeFinder;
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
     * @return \PhpParser\Node|mixed[]|int|null
     */
    public function refactor(Node $node)
    {
        if (! $this->isObjectType($node, new ObjectType('Illuminate\Database\Eloquent\Model'))) {
            return null;
        }

        $classMethods = $node->getMethods();

        foreach ($node->stmts as $key => $stmt) {
            if (! $stmt instanceof ClassMethod) {
                continue;
            }

            $newNode = $this->refactorClassMethod($stmt, $classMethods);

            if ($newNode === null) {
                continue;
            }

            if ($newNode instanceof ClassMethod) {
                $node->stmts[$key] = $newNode;
            } elseif ($newNode === NodeTraverser::REMOVE_NODE) {
                unset($node->stmts[$key]);
            }
        }

        return $node;
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

    /**
     * @param  ClassMethod[]  $allClassMethods
     * @return \PhpParser\Node\Stmt\ClassMethod|int|null
     */
    private function refactorClassMethod(ClassMethod $classMethod, array $allClassMethods)
    {
        $nodeName = $classMethod->name->name;

        if (! $this->isAccessor($nodeName) && ! $this->isMutator($nodeName)) {
            return null;
        }

        $attributeName = $this->parseAttributeName($nodeName);

        if ($attributeName === '') {
            return null;
        }

        // Skip if the new attribute name is already used
        foreach ($allClassMethods as $allClassMethod) {
            if ($this->isName($allClassMethod, $attributeName)) {
                return null;
            }
        }

        if ($this->isAccessor($nodeName)) {
            $mutator = $this->findPossibleMutator($allClassMethods, $attributeName);
            $accessor = $classMethod;
        } else {
            $accessor = $this->findPossibleAccessor($allClassMethods, $attributeName);
            $mutator = $classMethod;
        }

        // This means we have both an accessor and a mutator
        // So we generate the new method where the accessor
        // is placed on the model and remove the mutator,
        // so we don't run the refactoring twice
        if ($accessor instanceof ClassMethod && $mutator instanceof ClassMethod && $this->isMutator($nodeName)) {
            return NodeTraverser::REMOVE_NODE;
        }

        if ($accessor instanceof ClassMethod && $mutator instanceof ClassMethod) {
            $newNode = $this->createAccessorAndMutator($accessor, $mutator, $attributeName);
        } elseif ($accessor instanceof ClassMethod) {
            $newNode = $this->createAccessor($attributeName, $classMethod);
        } else {
            $newNode = $this->createMutator($attributeName, $classMethod);
        }

        // Preserve docblock
        $docComment = $classMethod->getDocComment();

        if ($docComment instanceof Doc) {
            $newNode->setDocComment($docComment);
        }

        return $newNode;
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
     * @param  array<Stmt>|null  $statements
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
        return strncmp($nodeName, 'get', strlen('get')) === 0 && substr_compare($nodeName, 'Attribute', -strlen('Attribute')) === 0;
    }

    private function isMutator(string $nodeName): bool
    {
        return strncmp($nodeName, 'set', strlen('set')) === 0 && substr_compare($nodeName, 'Attribute', -strlen('Attribute')) === 0;
    }

    /**
     * @param  ClassMethod[]  $allClassMethods
     */
    private function findPossibleAccessor(array $allClassMethods, string $attributeName): ?ClassMethod
    {
        foreach ($allClassMethods as $allClassMethod) {
            if ($allClassMethod->name->toString() === 'get' . ucfirst($attributeName) . 'Attribute') {
                return $allClassMethod;
            }
        }

        return null;
    }

    /**
     * @param  ClassMethod[]  $allClassMethods
     */
    private function findPossibleMutator(array $allClassMethods, string $attributeName): ?ClassMethod
    {
        foreach ($allClassMethods as $allClassMethod) {
            if ($allClassMethod->name->toString() === 'set' . ucfirst($attributeName) . 'Attribute') {
                return $allClassMethod;
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
