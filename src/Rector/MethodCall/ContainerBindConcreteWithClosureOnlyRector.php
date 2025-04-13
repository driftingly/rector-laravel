<?php

namespace RectorLaravel\Rector\MethodCall;

use PhpParser\Node;
use PhpParser\Node\Const_;
use PhpParser\Node\Expr\Closure;
use PhpParser\Node\Expr\MethodCall;
use PHPStan\Type\ObjectType;
use PHPStan\Type\Type;
use Rector\PHPStanStaticTypeMapper\Enum\TypeKind;
use Rector\Rector\AbstractRector;
use Rector\StaticTypeMapper\StaticTypeMapper;
use Rector\TypeDeclaration\TypeInferer\ReturnTypeInferer;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

final class ContainerBindConcreteWithClosureOnlyRector extends AbstractRector
{
    /**
     * @readonly
     */
    private ReturnTypeInferer $returnTypeInferer;
    /**
     * @readonly
     */
    private StaticTypeMapper $staticTypeMapper;
    public function __construct(ReturnTypeInferer $returnTypeInferer, StaticTypeMapper $staticTypeMapper)
    {
        $this->returnTypeInferer = $returnTypeInferer;
        $this->staticTypeMapper = $staticTypeMapper;
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(
            'Drop the specified abstract class from the bind method and replace it with a closure that returns the abstract class.',
            [
                new CodeSample(
                    <<<'CODE_SAMPLE'
$this->app->bind(SomeClass::class, function (): SomeClass {
    return new SomeClass();
});
CODE_SAMPLE
                    ,
                    <<<'CODE_SAMPLE'
$this->app->bind(function (): SomeClass {
    return new SomeClass();
});
CODE_SAMPLE
                ),
            ]
        );
    }

    public function getNodeTypes(): array
    {
        return [MethodCall::class];
    }

    /**
     * @param  MethodCall  $node
     */
    public function refactor(Node $node): ?MethodCall
    {
        if (! $this->isNames($node->name, ['bind', 'singleton', 'bindIf', 'singletonIf'])) {
            return null;
        }

        if (! $this->isObjectType($node->var, new ObjectType('Illuminate\Contracts\Container\Container'))) {
            return null;
        }

        if ($node->isFirstClassCallable()) {
            return null;
        }

        if (count($node->getArgs()) < 2) {
            return null;
        }

        $type = $this->getType($node->getArgs()[0]->value);
        $classString = $node->getArgs()[0]->value;
        $concreteNode = $node->getArgs()[1]->value;

        if (! $concreteNode instanceof Closure) {
            return null;
        }
        $abstractFromConcrete = $this->returnTypeInferer->inferFunctionLike($concreteNode);

        if ($classString instanceof Const_
        && $this->isName($classString, 'class')) {
            return null;
        }

        $abstractObjectType = $type->getClassStringObjectType();

        if ($abstractFromConcrete->isSuperTypeOf($abstractObjectType)->no()) {
            return null;
        }

        // set the concrete's return type of the closure to from what's determined in PHPStan
        $returnTypeNode = $this->staticTypeMapper->mapPHPStanTypeToPhpParserNode($abstractObjectType, TypeKind::RETURN);
        if (! $returnTypeNode instanceof Node) {
            return null;
        }

        $concreteNode->returnType = $returnTypeNode;

        $args = $node->getArgs();

        $node->args = array_splice($args, 1);

        return $node;
    }
}
