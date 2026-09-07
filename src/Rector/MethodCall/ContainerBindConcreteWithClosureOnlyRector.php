<?php

namespace RectorLaravel\Rector\MethodCall;

use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr\Closure;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Identifier;
use PHPStan\Type\ObjectType;
use Rector\PHPStanStaticTypeMapper\Enum\TypeKind;
use Rector\StaticTypeMapper\StaticTypeMapper;
use Rector\TypeDeclaration\TypeInferer\ReturnTypeInferer;
use Rector\VersionBonding\Contract\ComposerPackageConstraintInterface;
use Rector\VersionBonding\ValueObject\ComposerPackageConstraint;
use RectorLaravel\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

final class ContainerBindConcreteWithClosureOnlyRector extends AbstractRector implements ComposerPackageConstraintInterface
{
    public function __construct(
        private readonly ReturnTypeInferer $returnTypeInferer,
        private readonly StaticTypeMapper $staticTypeMapper,
    ) {}

    public function provideComposerPackageConstraint(): ComposerPackageConstraint
    {
        return new ComposerPackageConstraint('laravel/framework', '>=12.0');
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
        // bindIf() and singletonIf() are left out on purpose: they pass the abstract
        // straight to bound(), which uses it as an array offset, so a closure
        // abstract fatals there
        if (! $this->isNames($node->name, ['bind', 'singleton'])) {
            return null;
        }

        if (! $this->isObjectType($node->var, new ObjectType('Illuminate\Contracts\Container\Container'))) {
            return null;
        }

        if ($node->isFirstClassCallable()) {
            return null;
        }

        $abstractArg = $node->getArg('abstract', 0);
        $concreteArg = $node->getArg('concrete', 1);
        // singleton() has no $shared parameter, and passing one would be fatal
        $sharedArg = $this->isName($node->name, 'bind')
            ? $node->getArg('shared', 2)
            : null;

        if (! $abstractArg instanceof Arg || ! $concreteArg instanceof Arg) {
            return null;
        }

        // getArg() cannot match a spread, and refuses a name that is not a parameter;
        // rebuilding the call below would drop whatever it left behind
        if (count(array_filter([$abstractArg, $concreteArg, $sharedArg])) !== count($node->getArgs())) {
            return null;
        }

        $classString = $abstractArg->value;
        $concreteNode = $concreteArg->value;

        $type = $this->getType($classString);

        if ($classString instanceof Variable) {
            return null;
        }

        if (! $concreteNode instanceof Closure) {
            return null;
        }
        $abstractFromConcrete = $this->returnTypeInferer->inferFunctionLike($concreteNode);

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

        // the closure takes over the abstract position, so it must not stay named
        $concreteArg->name = null;

        $args = [$concreteArg];

        if ($sharedArg instanceof Arg) {
            // shared is no longer the third argument, so it has to be passed by name
            $sharedArg->name = new Identifier('shared');
            $args[] = $sharedArg;
        }

        $node->args = $args;

        return $node;
    }
}
