<?php

namespace RectorLaravel\Rector\Class_;

use PhpParser\BuilderFactory;
use PhpParser\Node;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Property;
use PhpParser\Node\Stmt\Return_;
use PHPStan\PhpDocParser\Ast\PhpDoc\ReturnTagValueNode;
use PHPStan\PhpDocParser\Ast\PhpDoc\VarTagValueNode;
use PHPStan\Type\ObjectType;
use Rector\BetterPhpDocParser\PhpDocInfo\PhpDocInfoFactory;
use Rector\Comments\NodeDocBlock\DocBlockUpdater;
use Rector\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \RectorLaravel\Tests\Rector\Class_\ModelCastsPropertyToCastsMethodRector\ModelCastsPropertyToCastsMethodRectorTest
 */
class ModelCastsPropertyToCastsMethodRector extends AbstractRector
{
    /**
     * @var \PhpParser\BuilderFactory
     */
    protected $builderFactory;
    /**
     * @var \Rector\BetterPhpDocParser\PhpDocInfo\PhpDocInfoFactory
     */
    protected $phpDocInfoFactory;
    /**
     * @var \Rector\Comments\NodeDocBlock\DocBlockUpdater
     */
    protected $docBlockUpdater;
    public function __construct(BuilderFactory $builderFactory, PhpDocInfoFactory $phpDocInfoFactory, DocBlockUpdater $docBlockUpdater)
    {
        $this->builderFactory = $builderFactory;
        $this->phpDocInfoFactory = $phpDocInfoFactory;
        $this->docBlockUpdater = $docBlockUpdater;
    }
    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Refactor Model $casts property with casts() method', [
            new CodeSample(<<<'CODE_SAMPLE'
use Illuminate\Database\Eloquent\Model;

class Person extends Model
{
    protected $casts = [
        'age' => 'integer',
    ];
}
CODE_SAMPLE
, <<<'CODE_SAMPLE'
use Illuminate\Database\Eloquent\Model;

class Person extends Model
{
    protected function casts(): array
    {
        return [
            'age' => 'integer',
        ];
    }
}
CODE_SAMPLE
),
        ]);
    }

    public function getNodeTypes(): array
    {
        return [Class_::class];
    }

    /**
     * @param  Class_  $node
     */
    public function refactor(Node $node): ?Class_
    {
        // Check if it is a Model
        if (! $this->isObjectType($node, new ObjectType('Illuminate\Database\Eloquent\Model'))) {

            return null;
        }

        // Check if there is already a casts() method
        foreach ($node->stmts as $stmt) {
            if ($stmt instanceof ClassMethod && $this->isName($stmt, 'casts')) {
                return null;
            }
        }

        // Check if there is a protected $casts property
        foreach ($node->stmts as $index => $stmt) {
            if ($stmt instanceof Property && ($this->isName($stmt, 'casts') && $stmt->isProtected())) {
                $method = $this->builderFactory->method('casts')
                    ->setReturnType('array')
                    ->makeProtected();

                if ($stmt->getDocComment() !== null) {
                    $method->setDocComment($stmt->getDocComment());
                }

                unset($node->stmts[$index]);

                // convert the property to a return statement
                $method->addStmt(new Return_($stmt->props[0]->default));
                $methodNode = $method->getNode();
                $node->stmts[] = $methodNode;

                $this->restorePhpDoc($methodNode);

                return $node;
            }
        }

        return null;
    }

    /**
     * @param \PhpParser\Node\Stmt\ClassMethod|\PhpParser\Node $methodNode
     */
    private function restorePhpDoc($methodNode): void
    {
        $phpDocInfo = $this->phpDocInfoFactory->createFromNodeOrEmpty($methodNode);

        $varTagValueNode = $phpDocInfo->getVarTagValueNode();

        if (! $varTagValueNode instanceof VarTagValueNode) {
            return;
        }

        $phpDocInfo->addTagValueNode(new ReturnTagValueNode($varTagValueNode->type, $varTagValueNode->description));
        $phpDocInfo->removeByType(VarTagValueNode::class);
        $this->docBlockUpdater->updateRefactoredNodeWithPhpDocInfo($methodNode);
    }
}
