<?php

namespace RectorLaravel\Rector\MethodCall;

use PhpParser\Node;
use Rector\PHPStanStaticTypeMapper\Enum\TypeKind;
use Rector\Rector\AbstractRector;
use PHPStan\Type\ObjectType;
use PHPStan\Type\Generic\GenericClassStringType;
use Rector\StaticTypeMapper\StaticTypeMapper;
use Rector\TypeDeclaration\TypeInferer\ReturnTypeInferer;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

final class ContainerBindConcreteWithClosureOnlyRector extends AbstractRector
{
    public function __construct(
        private readonly ReturnTypeInferer $returnTypeInferer,
        private readonly StaticTypeMapper $staticTypeMapper,
    )
    {
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
        return [Node\Expr\MethodCall::class];
    }

    /**
     * @param Node\Expr\MethodCall $node
     */
    public function refactor(Node $node): ?Node\Expr\MethodCall
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

        $abstract = $this->getType($node->getArgs()[0]->value);
        $concreteNode = $node->getArgs()[1]->value;

        if (! $concreteNode instanceof Node\Expr\Closure) {
            return null;
        }
        $abstractFromConcrete = $this->returnTypeInferer->inferFunctionLike($concreteNode);

        if (! $abstract instanceof GenericClassStringType) {
            return null;
        }

        $abstractObjectType = $abstract->getClassStringObjectType();

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
        return $this->nodeFactory->createMethodCall(
            $node->var,
            $node->name,
            array_splice($args, 1),
        );
    }
}
