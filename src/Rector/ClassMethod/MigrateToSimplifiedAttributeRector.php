<?php

declare(strict_types=1);

namespace RectorLaravel\Rector\ClassMethod;

use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr\Closure;
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

        if (!str_starts_with($name, 'get')) {
            return null;
        }

        if (!str_ends_with($name, 'Attribute')) {
            return null;
        }

        $newName = substr($name, strlen('get'));
        $newName = substr($newName, 0, -strlen('Attribute'));
        $newName = lcfirst($newName);

        if (empty($newName)) {
            return null;
        }

        /** @var ClassMethod $method */
        foreach ($parent->getMethods() as $method) {
            if ($this->isName($method, $newName)) {
                return null;
            }
        }

        $newMethod = new ClassMethod(
            new Identifier($newName),
            [
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

        if (! $classLike instanceof ClassLike) {
            return true;
        }

        if ($classLike instanceof Class_) {
            return ! $this->isObjectType($classLike, new ObjectType('Illuminate\Database\Eloquent\Model'));
        }

        return false;
    }
}
