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
use PhpParser\Node\Expr\BinaryOp\BitwiseOr;
use PhpParser\Node\Expr\ClassConstFetch;
use PhpParser\Node\Expr\ConstFetch;
use PhpParser\Node\Expr\New_;
use PhpParser\Node\Expr\PropertyFetch;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name;
use PhpParser\Node\Name\FullyQualified;
use PhpParser\Node\Scalar\String_;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Property;
use PhpParser\Node\Stmt\Return_;
use PHPStan\Reflection\ClassReflection;
use PHPStan\Type\ObjectType;
use Rector\NodeTypeResolver\Node\AttributeKey;
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
    private const string AS_COMMAND_ATTRIBUTE = 'Symfony\Component\Console\Attribute\AsCommand';

    private const string COMMAND_CLASS = 'Illuminate\Console\Command';

    private const string INPUT_ARGUMENT_CLASS = 'Symfony\Component\Console\Input\InputArgument';

    private const string INPUT_OPTION_CLASS = 'Symfony\Component\Console\Input\InputOption';

    /**
     * Once the signature is gone Laravel builds the definition from these
     * instead, so an existing implementation would start taking effect.
     *
     * @var string[]
     */
    private const array PARAMETER_METHODS = ['getArguments', 'getOptions'];

    /**
     * A bare command name, e.g. "mail:send". Anything else - leftover braces or
     * alias pipes - cannot be expressed by the attribute name on its own.
     */
    private const string COMMAND_NAME_REGEX = '#^[^\s:|{}]++(?::[^\s:|{}]++)*+$#';

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

        $propertyValue = $this->matchStringDefault($commandNameProperty);
        if (! $propertyValue instanceof String_) {
            return null;
        }

        $arguments = [];
        $options = [];

        if ($signatureProperty instanceof Property) {
            $parsedSignature = $this->parseSignature($propertyValue->value);
            if ($parsedSignature === null) {
                return null;
            }

            [$commandName, $arguments, $options] = $parsedSignature;

            // whatever the signature defined would now come from these instead
            if ($this->hasParameterMethod($node)) {
                return null;
            }
        } else {
            $commandName = $propertyValue->value;
        }

        if (preg_match(self::COMMAND_NAME_REGEX, $commandName) !== 1) {
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

        $args = [new Arg(new String_($commandName), name: new Identifier('name'))];
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

        if ($arguments !== []) {
            $node->stmts[] = $this->createParametersMethod('getArguments', $arguments);
        }

        if ($options !== []) {
            $node->stmts[] = $this->createParametersMethod('getOptions', $options);
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

        // the name is all that may sit outside the braces, otherwise the parser
        // and this rule are reading the signature differently
        $withoutTokens = preg_replace('/\{\s*.*?\s*\}/', '', $signature);
        if ($withoutTokens === null || trim($withoutTokens) !== $name) {
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
        $array->setAttribute(AttributeKey::NEWLINED_ARRAY_PRINT, true);

        return new ClassMethod($methodName, [
            'flags' => Modifiers::PROTECTED,
            'returnType' => new Identifier('array'),
            'stmts' => [new Return_($array)],
        ]);
    }

    private function hasParameterMethod(Class_ $class): bool
    {
        $classReflection = $this->reflectionResolver->resolveClassReflection($class);

        foreach (self::PARAMETER_METHODS as $methodName) {
            if ($class->getMethod($methodName) instanceof ClassMethod) {
                return true;
            }

            if (! $classReflection instanceof ClassReflection || ! $classReflection->hasNativeMethod($methodName)) {
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
