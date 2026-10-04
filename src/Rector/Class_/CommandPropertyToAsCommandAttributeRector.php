<?php

declare(strict_types=1);

namespace RectorLaravel\Rector\Class_;

use PhpParser\Modifiers;
use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\ArrayItem;
use PhpParser\Node\Attribute;
use PhpParser\Node\AttributeGroup;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\Array_;
use PhpParser\Node\Expr\ArrayDimFetch;
use PhpParser\Node\Expr\BinaryOp\BitwiseOr;
use PhpParser\Node\Expr\ClassConstFetch;
use PhpParser\Node\Expr\ConstFetch;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\New_;
use PhpParser\Node\Expr\NullsafePropertyFetch;
use PhpParser\Node\Expr\PostDec;
use PhpParser\Node\Expr\PostInc;
use PhpParser\Node\Expr\PreDec;
use PhpParser\Node\Expr\PreInc;
use PhpParser\Node\Expr\PropertyFetch;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name;
use PhpParser\Node\Name\FullyQualified;
use PhpParser\Node\PropertyItem;
use PhpParser\Node\Scalar\String_;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Property;
use PhpParser\Node\Stmt\Return_;
use PhpParser\NodeVisitor;
use PHPStan\Reflection\ClassReflection;
use PHPStan\Type\ObjectType;
use Rector\NodeTypeResolver\Node\AttributeKey;
use Rector\PhpParser\Node\Value\ValueResolver;
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
    private const string AS_COMMAND_ATTRIBUTE = 'Symfony\Component\Console\Attribute\AsCommand';

    private const string COMMAND_CLASS = 'Illuminate\Console\Command';

    private const string INPUT_ARGUMENT_CLASS = 'Symfony\Component\Console\Input\InputArgument';

    private const string INPUT_OPTION_CLASS = 'Symfony\Component\Console\Input\InputOption';

    /**
     * Once the signature is gone Laravel calls specifyParameters(), which builds
     * the definition from these instead.
     *
     * @var string[]
     */
    private const array PARAMETER_METHODS = ['getArguments', 'getOptions'];

    /**
     * Symfony keeps its own copy of these, which the attribute fills in, while
     * Laravel's properties are left at their defaults.
     *
     * @var array<string, string>
     */
    private const array PROPERTY_GETTERS = ['name' => 'getName', 'description' => 'getDescription'];

    /**
     * Context flags of a property fetch that is written to rather than read.
     *
     * @var string[]
     */
    private const array WRITE_CONTEXT_ATTRIBUTES = [
        AttributeKey::IS_BEING_ASSIGNED,
        AttributeKey::IS_ASSIGN_OP_VAR,
        AttributeKey::IS_ASSIGN_REF_EXPR,
        AttributeKey::IS_BYREF_VAR,
        AttributeKey::IS_ISSET_VAR,
        AttributeKey::IS_UNSET_VAR,
    ];

    /**
     * A bare command name, e.g. "mail:send". Anything else - leftover braces or
     * alias pipes - cannot be expressed by the attribute name on its own.
     */
    private const string COMMAND_NAME_REGEX = '#^[^\s:|]++(?::[^\s:|]++)*+$#';

    public function __construct(
        private readonly ReflectionResolver $reflectionResolver,
        private readonly ValueResolver $valueResolver,
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
    protected $signature = 'mail:send {user} {--queue}';

    protected $description = 'Send the queued emails';
}
CODE_SAMPLE,
                <<<'CODE_SAMPLE'
use Illuminate\Console\Command;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

#[AsCommand(name: 'mail:send', description: 'Send the queued emails')]
class SendEmails extends Command
{
    protected function getArguments(): array
    {
        return [new InputArgument('user', InputArgument::REQUIRED)];
    }

    protected function getOptions(): array
    {
        return [new InputOption('queue', null, InputOption::VALUE_NONE)];
    }
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

        $asCommandAttribute = $this->findAsCommandAttribute($node);
        if ($asCommandAttribute instanceof Attribute && ! $this->isMergeableAttribute($asCommandAttribute)) {
            return null;
        }

        $signatureProperty = $node->getProperty('signature');
        $nameProperty = $node->getProperty('name');

        $commandNameProperty = $signatureProperty ?? $nameProperty;
        if (! $commandNameProperty instanceof Property) {
            return null;
        }

        $commandNamePropertyName = $signatureProperty instanceof Property ? 'signature' : 'name';
        $propertyDefault = $this->matchDefault($commandNameProperty, $commandNamePropertyName);
        if (! $propertyDefault instanceof Expr) {
            return null;
        }

        // the value has to be known here, as the signature is parsed at this point
        $propertyValue = $this->valueResolver->getValue($propertyDefault);
        if (! is_string($propertyValue)) {
            return null;
        }

        $arguments = [];
        $options = [];

        if ($signatureProperty instanceof Property) {
            $parsedSignature = $this->parseSignature($propertyValue);
            if ($parsedSignature === null) {
                return null;
            }

            [$commandName, $arguments, $options] = $parsedSignature;

            // an own implementation would have to absorb the signature's
            // parameters, which is a merge this rule does not attempt
            if ($this->hasOwnParameterMethod($node)) {
                return null;
            }
        } else {
            $commandName = $propertyValue;
        }

        if (preg_match(self::COMMAND_NAME_REGEX, $commandName) !== 1) {
            return null;
        }

        $descriptionProperty = $node->getProperty('description');
        $description = null;
        $hasNullDescription = false;

        if ($descriptionProperty instanceof Property) {
            $description = $this->matchDefault($descriptionProperty, 'description');

            if (! $description instanceof Expr) {
                // a description that cannot be moved is simply left where it is
                $descriptionProperty = null;
            } elseif ($this->valueResolver->isNull($description) || $this->valueResolver->isValue($description, '')) {
                $hasNullDescription = $this->valueResolver->isNull($description);
                // Laravel ignores an empty description, so keep it out of the attribute
                $description = null;
            }
        }

        $removedPropertyNames = [$commandNamePropertyName];
        if ($descriptionProperty instanceof Property) {
            $removedPropertyNames[] = 'description';
        }

        // Laravel overwrites the name with the one parsed from the signature, so a
        // name next to a signature is dead and goes away with it
        if ($signatureProperty instanceof Property && $nameProperty instanceof Property) {
            $removedPropertyNames[] = 'name';
        }

        // the getter would hand back '' where the property held null
        $readablePropertyNames = $hasNullDescription ? ['name'] : array_keys(self::PROPERTY_GETTERS);

        // Laravel fills in the name from the signature even when it is not
        // declared, so its reads depend on the signature as well
        $fetchedPropertyNames = $signatureProperty instanceof Property
            ? array_unique([...$removedPropertyNames, 'name'])
            : $removedPropertyNames;

        $propertyFetches = $this->matchGetterReplaceablePropertyFetches($node, $fetchedPropertyNames, $readablePropertyNames);
        if ($propertyFetches === null) {
            return null;
        }

        if ($asCommandAttribute instanceof Attribute) {
            // the properties win over the attribute at runtime, though an empty
            // description leaves the attribute's one in place
            $this->setAttributeArg($asCommandAttribute, 'name', 0, new String_($commandName));
            if ($description instanceof Expr) {
                $this->setAttributeArg($asCommandAttribute, 'description', 1, $description);
            }
        } else {
            $args = [new Arg(new String_($commandName), name: new Identifier('name'))];
            if ($description instanceof Expr) {
                // a property default is a constant expression, which an attribute
                // argument accepts just the same
                $args[] = new Arg($description, name: new Identifier('description'));
            }

            $node->attrGroups[] = new AttributeGroup([
                new Attribute(new FullyQualified(self::AS_COMMAND_ATTRIBUTE), $args),
            ]);
        }

        $this->removeProperties($node, $removedPropertyNames);
        $this->replacePropertyFetchesWithGetters($node, $propertyFetches);

        // only the signature suppressed specifyParameters(); on the name path it
        // already runs, so the inherited methods must be left exactly as they are
        if ($signatureProperty instanceof Property) {
            if ($arguments !== [] || $this->inheritsParameterMethod($node, 'getArguments')) {
                $node->stmts[] = $this->createParametersMethod('getArguments', $arguments);
            }

            if ($options !== [] || $this->inheritsParameterMethod($node, 'getOptions')) {
                $node->stmts[] = $this->createParametersMethod('getOptions', $options);
            }
        }

        return $node;
    }

    public function provideMinPhpVersion(): int
    {
        return PhpVersionFeature::ATTRIBUTES;
    }

    private function matchDefault(Property $property, string $propertyName): ?Expr
    {
        if (! $property->isProtected()) {
            return null;
        }

        $default = null;
        foreach ($property->props as $propertyItem) {
            if ($this->isName($propertyItem, $propertyName)) {
                $default = $propertyItem->default;
            }
        }

        return $default;
    }

    /**
     * Mirrors \Illuminate\Console\Parser, which is what the signature would
     * otherwise be handed to at runtime.
     *
     * @return array{string, New_[], New_[]}|null
     */
    private function parseSignature(string $signature): ?array
    {
        if (preg_match('/[^\s]+/', $signature, $nameMatches) !== 1) {
            return null;
        }

        $name = $nameMatches[0];

        if (preg_match_all('/\{\s*(.*?)\s*\}/', $signature, $tokenMatches) === false) {
            return null;
        }

        $arguments = [];
        $options = [];

        foreach ($tokenMatches[1] as $token) {
            if (preg_match('/^-{2,}(.*)/', $token, $optionMatches) === 1) {
                $option = $this->parseOption($optionMatches[1]);
                if (! $option instanceof New_) {
                    return null;
                }

                $options[] = $option;
            } else {
                $arguments[] = $this->parseArgument($token);
            }
        }

        return [$name, $arguments, $options];
    }

    private function parseArgument(string $token): New_
    {
        [$token, $description] = $this->extractDescription($token);

        if (str_ends_with($token, '?*')) {
            return $this->createArgument(trim($token, '?*'), ['IS_ARRAY'], $description);
        }

        if (str_ends_with($token, '*')) {
            return $this->createArgument(trim($token, '*'), ['IS_ARRAY', 'REQUIRED'], $description);
        }

        if (str_ends_with($token, '?')) {
            return $this->createArgument(trim($token, '?'), ['OPTIONAL'], $description);
        }

        if (preg_match('/(.+)\=\*(.+)/', $token, $matches) === 1) {
            return $this->createArgument($matches[1], ['IS_ARRAY'], $description, $this->createArrayDefault($matches[2]));
        }

        if (preg_match('/(.+)\=(.+)/', $token, $matches) === 1) {
            return $this->createArgument($matches[1], ['OPTIONAL'], $description, new String_($matches[2]));
        }

        return $this->createArgument($token, ['REQUIRED'], $description);
    }

    private function parseOption(string $token): ?New_
    {
        [$token, $description] = $this->extractDescription($token);

        $shortcutParts = preg_split('/\s*\|\s*/', $token, 2);
        if ($shortcutParts === false) {
            return null;
        }

        $shortcut = null;
        if (isset($shortcutParts[1])) {
            $shortcut = $shortcutParts[0];
            $token = $shortcutParts[1];
        }

        if (str_ends_with($token, '=')) {
            return $this->createOption(trim($token, '='), $shortcut, ['VALUE_OPTIONAL'], $description);
        }

        if (str_ends_with($token, '=*')) {
            return $this->createOption(trim($token, '=*'), $shortcut, ['VALUE_OPTIONAL', 'VALUE_IS_ARRAY'], $description);
        }

        if (preg_match('/(.+)\=\*(.+)/', $token, $matches) === 1) {
            return $this->createOption($matches[1], $shortcut, ['VALUE_OPTIONAL', 'VALUE_IS_ARRAY'], $description, $this->createArrayDefault($matches[2]));
        }

        if (preg_match('/(.+)\=(.+)/', $token, $matches) === 1) {
            return $this->createOption($matches[1], $shortcut, ['VALUE_OPTIONAL'], $description, new String_($matches[2]));
        }

        return $this->createOption($token, $shortcut, ['VALUE_NONE'], $description);
    }

    /**
     * @return array{string, string}
     */
    private function extractDescription(string $token): array
    {
        $parts = preg_split('/\s+:\s+/', trim($token), 2);
        if ($parts === false || count($parts) !== 2) {
            return [$token, ''];
        }

        return [$parts[0], $parts[1]];
    }

    /**
     * @param  string[]  $modes
     */
    private function createArgument(string $name, array $modes, string $description, ?Expr $expr = null): New_
    {
        $args = [
            new Arg(new String_($name)),
            new Arg($this->createModeExpr(self::INPUT_ARGUMENT_CLASS, $modes)),
        ];

        return new New_(
            new FullyQualified(self::INPUT_ARGUMENT_CLASS),
            $this->appendDescriptionAndDefault($args, $description, $expr)
        );
    }

    /**
     * @param  string[]  $modes
     */
    private function createOption(string $name, ?string $shortcut, array $modes, string $description, ?Expr $expr = null): New_
    {
        $args = [
            new Arg(new String_($name)),
            new Arg($shortcut === null ? new ConstFetch(new Name('null')) : new String_($shortcut)),
            new Arg($this->createModeExpr(self::INPUT_OPTION_CLASS, $modes)),
        ];

        return new New_(
            new FullyQualified(self::INPUT_OPTION_CLASS),
            $this->appendDescriptionAndDefault($args, $description, $expr)
        );
    }

    /**
     * Both constructors default the description to '' and the default to null,
     * so those arguments are only worth printing when they carry something.
     *
     * @param  Arg[]  $args
     * @return Arg[]
     */
    private function appendDescriptionAndDefault(array $args, string $description, ?Expr $expr): array
    {
        if ($description === '' && ! $expr instanceof Expr) {
            return $args;
        }

        $args[] = new Arg(new String_($description));

        if ($expr instanceof Expr) {
            $args[] = new Arg($expr);
        }

        return $args;
    }

    /**
     * @param  string[]  $modes
     */
    private function createModeExpr(string $className, array $modes): Expr
    {
        $expr = null;

        foreach ($modes as $mode) {
            $constFetch = new ClassConstFetch(new FullyQualified($className), new Identifier($mode));
            $expr = $expr instanceof Expr ? new BitwiseOr($expr, $constFetch) : $constFetch;
        }

        // every branch above passes at least one mode
        return $expr instanceof Expr ? $expr : new ConstFetch(new Name('null'));
    }

    private function createArrayDefault(string $default): Array_
    {
        $values = preg_split('/,\s?/', $default);
        if ($values === false) {
            $values = [$default];
        }

        return new Array_(
            array_map(static fn (string $value): ArrayItem => new ArrayItem(new String_($value)), $values),
            ['kind' => Array_::KIND_SHORT]
        );
    }

    /**
     * @param  New_[]  $parameters
     */
    private function createParametersMethod(string $methodName, array $parameters): ClassMethod
    {
        $array = new Array_(
            array_map(static fn (New_ $new): ArrayItem => new ArrayItem($new), $parameters),
            ['kind' => Array_::KIND_SHORT]
        );

        // one parameter per line, these get long quickly
        if ($parameters !== []) {
            $array->setAttribute(AttributeKey::NEWLINED_ARRAY_PRINT, true);
        }

        return new ClassMethod($methodName, [
            'flags' => Modifiers::PROTECTED,
            'returnType' => new Identifier('array'),
            'stmts' => [new Return_($array)],
        ]);
    }

    private function hasOwnParameterMethod(Class_ $class): bool
    {
        foreach (self::PARAMETER_METHODS as $methodName) {
            if ($class->getMethod($methodName) instanceof ClassMethod) {
                return true;
            }
        }

        return false;
    }

    /**
     * Inherited from somewhere other than the framework's own no-op, so once
     * specifyParameters() runs it would start taking effect. Declaring the
     * method here shadows it and keeps the definition as the signature had it.
     */
    private function inheritsParameterMethod(Class_ $class, string $methodName): bool
    {
        $classReflection = $this->reflectionResolver->resolveClassReflection($class);
        if (! $classReflection instanceof ClassReflection || ! $classReflection->hasNativeMethod($methodName)) {
            return false;
        }

        return $classReflection->getNativeMethod($methodName)->getDeclaringClass()->getName() !== self::COMMAND_CLASS;
    }

    /**
     * Collects the reads of the removed properties, which keep working through
     * the getters, or returns null when any other use is found.
     *
     * @param  string[]  $propertyNames
     * @param  string[]  $readablePropertyNames
     * @return PropertyFetch[]|null
     */
    private function matchGetterReplaceablePropertyFetches(Class_ $class, array $propertyNames, array $readablePropertyNames): ?array
    {
        $propertyFetches = [];
        $isReplaceable = true;

        $this->traverseNodesWithCallable($class->stmts, function (Node $node) use ($propertyNames, $readablePropertyNames, &$propertyFetches, &$isReplaceable): ?int {
            // $this inside a nested class refers to that class
            if ($node instanceof Class_) {
                return NodeVisitor::DONT_TRAVERSE_CHILDREN;
            }

            if ($node instanceof PreInc || $node instanceof PreDec || $node instanceof PostInc || $node instanceof PostDec
                || ($node instanceof ArrayDimFetch && $this->isWriteContext($node))) {
                if ($this->isPropertyFetchOf($node->var, $propertyNames)) {
                    $isReplaceable = false;
                }

                return null;
            }

            if ($node instanceof NullsafePropertyFetch && $this->isNames($node->name, $propertyNames)) {
                $isReplaceable = false;

                return null;
            }

            if (! $node instanceof PropertyFetch || ! $this->isNames($node->name, $propertyNames)) {
                return null;
            }

            if (! $this->isNames($node->name, $readablePropertyNames)
                || ! $node->var instanceof Variable
                || ! $this->isName($node->var, 'this')
                || $this->isWriteContext($node)) {
                $isReplaceable = false;

                return null;
            }

            $propertyFetches[] = $node;

            return null;
        });

        return $isReplaceable ? $propertyFetches : null;
    }

    /**
     * @param  PropertyFetch[]  $propertyFetches
     */
    private function replacePropertyFetchesWithGetters(Class_ $class, array $propertyFetches): void
    {
        $this->traverseNodesWithCallable($class->stmts, function (Node $node) use ($propertyFetches): ?MethodCall {
            if (! $node instanceof PropertyFetch || ! in_array($node, $propertyFetches, true)) {
                return null;
            }

            $propertyName = $this->getName($node->name);
            if ($propertyName === null) {
                return null;
            }

            return new MethodCall($node->var, self::PROPERTY_GETTERS[$propertyName]);
        });
    }

    /**
     * @param  string[]  $propertyNames
     */
    private function isPropertyFetchOf(Expr $expr, array $propertyNames): bool
    {
        return $expr instanceof PropertyFetch && $this->isNames($expr->name, $propertyNames);
    }

    private function isWriteContext(Node $node): bool
    {
        foreach (self::WRITE_CONTEXT_ATTRIBUTES as $attribute) {
            if ($node->getAttribute($attribute) === true) {
                return true;
            }
        }

        return false;
    }

    /**
     * Removes the items one by one, so the rest of a grouped declaration stays.
     *
     * @param  string[]  $propertyNames
     */
    private function removeProperties(Class_ $class, array $propertyNames): void
    {
        foreach ($class->stmts as $key => $stmt) {
            if (! $stmt instanceof Property) {
                continue;
            }

            $stmt->props = array_values(array_filter(
                $stmt->props,
                fn (PropertyItem $propertyItem): bool => ! $this->isNames($propertyItem, $propertyNames)
            ));

            if ($stmt->props === []) {
                unset($class->stmts[$key]);
            }
        }
    }

    private function findAsCommandAttribute(Class_ $class): ?Attribute
    {
        foreach ($class->attrGroups as $attrGroup) {
            foreach ($attrGroup->attrs as $attribute) {
                if ($this->isName($attribute->name, self::AS_COMMAND_ATTRIBUTE)) {
                    return $attribute;
                }
            }
        }

        return null;
    }

    /**
     * The name Laravel passes on makes Symfony ignore the attribute's aliases
     * and hidden flag, which would start to apply once the properties are gone.
     */
    private function isMergeableAttribute(Attribute $attribute): bool
    {
        foreach ($attribute->args as $position => $arg) {
            if ($arg->name instanceof Identifier
                ? $this->isNames($arg->name, ['aliases', 'hidden'])
                : $position >= 2) {
                return false;
            }
        }

        $nameArg = $this->findAttributeArg($attribute, 'name', 0);
        if (! $nameArg instanceof Arg) {
            return true;
        }

        $name = $this->valueResolver->getValue($nameArg->value);

        return is_string($name) && ! str_contains($name, '|');
    }

    private function setAttributeArg(Attribute $attribute, string $name, int $position, Expr $expr): void
    {
        $arg = $this->findAttributeArg($attribute, $name, $position);
        if (! $arg instanceof Arg) {
            $attribute->args[] = new Arg($expr, name: new Identifier($name));

            return;
        }

        $value = $this->valueResolver->getValue($arg->value);
        if ($value === null || $value !== $this->valueResolver->getValue($expr)) {
            $arg->value = $expr;
        }
    }

    private function findAttributeArg(Attribute $attribute, string $name, int $position): ?Arg
    {
        foreach ($attribute->args as $argPosition => $arg) {
            if ($arg->name instanceof Identifier ? $this->isName($arg->name, $name) : $argPosition === $position) {
                return $arg;
            }
        }

        return null;
    }
}
