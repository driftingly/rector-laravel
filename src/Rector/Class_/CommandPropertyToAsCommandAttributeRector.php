<?php

declare(strict_types=1);

namespace RectorLaravel\Rector\Class_;

use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Attribute;
use PhpParser\Node\AttributeGroup;
use PhpParser\Node\Expr\PropertyFetch;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name\FullyQualified;
use PhpParser\Node\Scalar\String_;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Property;
use PHPStan\Type\ObjectType;
use Rector\Php80\NodeAnalyzer\PhpAttributeAnalyzer;
use Rector\PhpParser\Node\BetterNodeFinder;
use Rector\Reflection\ReflectionResolver;
use Rector\ValueObject\PhpVersionFeature;
use Rector\VersionBonding\Contract\ComposerPackageConstraintInterface;
use Rector\VersionBonding\Contract\MinPhpVersionInterface;
use Rector\VersionBonding\ValueObject\ComposerPackageConstraint;
use RectorLaravel\AbstractRector;
use RectorLaravel\Tests\Rector\Class_\CommandPropertyToAsCommandAttributeRector\CommandPropertyToAsCommandAttributeRectorTest;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see CommandPropertyToAsCommandAttributeRectorTest
 */
final class CommandPropertyToAsCommandAttributeRector extends AbstractRector implements ComposerPackageConstraintInterface, MinPhpVersionInterface
{
    /**
     * @var string
     */
    private const AS_COMMAND_ATTRIBUTE = 'Symfony\Component\Console\Attribute\AsCommand';

    /**
     * @var string
     */
    private const COMMAND_CLASS = 'Illuminate\Console\Command';

    /**
     * Laravel only falls back to the attribute when the signature does not
     * define the parameters itself, which is what these methods are for.
     *
     * @var string[]
     */
    private const PARAMETER_METHODS = ['getArguments', 'getOptions'];

    /**
     * A bare command name, e.g. "mail:send". Anything else - arguments, options
     * or alias pipes - cannot be expressed by the attribute name on its own.
     *
     * @var string
     */
    private const COMMAND_NAME_REGEX = '#^[^\s:|{}]++(?::[^\s:|{}]++)*+$#';

    public function __construct(
        private readonly PhpAttributeAnalyzer $phpAttributeAnalyzer,
        private readonly BetterNodeFinder $betterNodeFinder,
        private readonly ReflectionResolver $reflectionResolver,
    ) {}

    /**
     * The attribute is only picked up from Symfony Console 6, which ships with
     * Laravel 9 and up.
     */
    public function provideComposerPackageConstraint(): ComposerPackageConstraint
    {
        return new ComposerPackageConstraint('laravel/framework', '>=9.0');
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(
            'Changes the name/signature and description properties of a console command to the AsCommand attribute',
            [new CodeSample(
                <<<'CODE_SAMPLE'
use Illuminate\Console\Command;

class SendEmails extends Command
{
    protected $signature = 'mail:send';

    protected $description = 'Send the queued emails';
}
CODE_SAMPLE,
                <<<'CODE_SAMPLE'
use Illuminate\Console\Command;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(name: 'mail:send', description: 'Send the queued emails')]
class SendEmails extends Command
{
}
CODE_SAMPLE
            )]
        );
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
     */
    public function refactor(Node $node): ?Node
    {
        if ($node->isAbstract() || $node->isAnonymous()) {
            return null;
        }

        if (! $this->isObjectType($node, new ObjectType(self::COMMAND_CLASS))) {
            return null;
        }

        if ($this->phpAttributeAnalyzer->hasPhpAttribute($node, self::AS_COMMAND_ATTRIBUTE)) {
            return null;
        }

        $signatureProperty = $node->getProperty('signature');
        $nameProperty = $node->getProperty('name');

        // the signature silently wins over the name, so leave that call to a human
        if ($signatureProperty instanceof Property && $nameProperty instanceof Property) {
            return null;
        }

        $commandNameProperty = $signatureProperty ?? $nameProperty;
        if (! $commandNameProperty instanceof Property) {
            return null;
        }

        $commandName = $this->matchStringDefault($commandNameProperty);
        if (! $commandName instanceof String_) {
            return null;
        }

        if (preg_match(self::COMMAND_NAME_REGEX, $commandName->value) !== 1) {
            return null;
        }

        // dropping the signature turns getArguments()/getOptions() back on
        if ($signatureProperty instanceof Property && $this->hasParameterMethod($node)) {
            return null;
        }

        $descriptionProperty = $node->getProperty('description');
        $description = null;

        if ($descriptionProperty instanceof Property) {
            $description = $this->matchStringDefault($descriptionProperty);

            if (! $description instanceof String_) {
                // a description that cannot be moved is simply left where it is
                $descriptionProperty = null;
            } elseif ($description->value === '') {
                // an empty description is the default, so keep it out of the attribute
                $description = null;
            }
        }

        $removedProperties = $descriptionProperty instanceof Property
            ? [$commandNameProperty, $descriptionProperty]
            : [$commandNameProperty];
        if ($this->isPropertyUsedInClass($node, $removedProperties)) {
            return null;
        }

        $args = [new Arg($commandName, name: new Identifier('name'))];
        if ($description instanceof String_) {
            $args[] = new Arg($description, name: new Identifier('description'));
        }

        $node->attrGroups[] = new AttributeGroup([
            new Attribute(new FullyQualified(self::AS_COMMAND_ATTRIBUTE), $args),
        ]);

        foreach ($node->stmts as $key => $stmt) {
            if (in_array($stmt, $removedProperties, true)) {
                unset($node->stmts[$key]);
            }
        }

        return $node;
    }

    public function provideMinPhpVersion(): int
    {
        return PhpVersionFeature::ATTRIBUTES;
    }

    private function matchStringDefault(Property $property): ?String_
    {
        if (! $property->isProtected() || $property->isStatic() || $property->isReadonly()) {
            return null;
        }

        if (count($property->props) !== 1) {
            return null;
        }

        $default = $property->props[0]->default;
        if (! $default instanceof String_) {
            return null;
        }

        return new String_($default->value);
    }

    private function hasParameterMethod(Class_ $class): bool
    {
        $classReflection = $this->reflectionResolver->resolveClassReflection($class);

        foreach (self::PARAMETER_METHODS as $methodName) {
            if ($class->getMethod($methodName) instanceof ClassMethod) {
                return true;
            }

            if ($classReflection === null || ! $classReflection->hasNativeMethod($methodName)) {
                continue;
            }

            // the framework's own no-op implementations are not an override
            if ($classReflection->getNativeMethod($methodName)->getDeclaringClass()->getName() !== self::COMMAND_CLASS) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param  Property[]  $properties
     */
    private function isPropertyUsedInClass(Class_ $class, array $properties): bool
    {
        $propertyNames = [];
        foreach ($properties as $property) {
            $propertyName = $this->getName($property->props[0]);
            if ($propertyName !== null) {
                $propertyNames[] = $propertyName;
            }
        }

        return (bool) $this->betterNodeFinder->findFirst(
            $class->stmts,
            fn (Node $node): bool => $node instanceof PropertyFetch
                && $this->isNames($node->name, $propertyNames)
        );
    }
}
