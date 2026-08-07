<?php

declare(strict_types=1);

namespace RectorLaravel\Rector\MethodCall;

use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr\ClassConstFetch;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name\FullyQualified;
use PhpParser\Node\Scalar\String_;
use PHPStan\Reflection\ClassReflection;
use PHPStan\Reflection\ReflectionProvider;
use PHPStan\Type\ObjectType;
use Rector\Contract\Rector\ConfigurableRectorInterface;
use RectorLaravel\AbstractRector;
use RectorLaravel\Tests\Rector\MethodCall\RelationTableStringToPivotClassRector\RelationTableStringToPivotClassRectorTest;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\ConfiguredCodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;
use Webmozart\Assert\Assert;

/**
 * @see RelationTableStringToPivotClassRectorTest
 */
final class RelationTableStringToPivotClassRector extends AbstractRector implements ConfigurableRectorInterface
{
    public const string MODEL_NAMESPACES = 'model_namespaces';

    private const string MODEL_CLASS = 'Illuminate\Database\Eloquent\Model';

    private const string TABLE_ATTRIBUTE_CLASS = 'Illuminate\Database\Eloquent\Attributes\Table';

    /**
     * Position of the $table parameter within each relation method.
     */
    private const array TABLE_ARGUMENT_POSITIONS = [
        'belongsToMany' => 1,
        'morphToMany' => 2,
        'morphedByMany' => 2,
    ];

    /**
     * @var list<string>
     */
    private array $modelNamespaces = ['App\Models'];

    public function __construct(private readonly ReflectionProvider $reflectionProvider) {}

    public function getRuleDefinition(): RuleDefinition
    {
        $description = "Changes the pivot table name of a many to many relation to the pivot model class.\n\n"
            . 'The namespaces searched for the pivot model can be configured with '
            . '`[RelationTableStringToPivotClassRector::MODEL_NAMESPACES => [\'App\Models\']]`.';

        return new RuleDefinition(
            $description,
            [
                new ConfiguredCodeSample(
                    <<<'CODE_SAMPLE'
$this->belongsToMany(Tag::class, 'post_tag');
CODE_SAMPLE
                    ,
                    <<<'CODE_SAMPLE'
$this->belongsToMany(Tag::class, \App\Models\PostTag::class);
CODE_SAMPLE
                    ,
                    [RelationTableStringToPivotClassRector::MODEL_NAMESPACES => ['App\Models']]
                ),
            ]
        );
    }

    /**
     * @return array<class-string<Node>>
     */
    public function getNodeTypes(): array
    {
        return [MethodCall::class];
    }

    public function configure(array $configuration): void
    {
        if ($configuration === []) {
            return;
        }

        Assert::keyExists($configuration, self::MODEL_NAMESPACES);
        Assert::isArray($configuration[self::MODEL_NAMESPACES]);
        Assert::allStringNotEmpty($configuration[self::MODEL_NAMESPACES]);

        $this->modelNamespaces = array_values($configuration[self::MODEL_NAMESPACES]);
    }

    /**
     * @param  MethodCall  $node
     */
    public function refactor(Node $node): ?MethodCall
    {
        $tableArgument = $this->getTableArgument($node);

        if (! $tableArgument instanceof Arg) {
            return null;
        }

        if (! $this->isObjectType($node->var, new ObjectType(self::MODEL_CLASS))) {
            return null;
        }

        $table = $tableArgument->value;

        if (! $table instanceof String_) {
            return null;
        }

        $pivotClass = $this->resolvePivotClass($table->value);

        if ($pivotClass === null) {
            return null;
        }

        $tableArgument->value = new ClassConstFetch(new FullyQualified($pivotClass), 'class');

        return $node;
    }

    private function getTableArgument(MethodCall $methodCall): ?Arg
    {
        foreach (self::TABLE_ARGUMENT_POSITIONS as $method => $position) {
            if (! $this->isName($methodCall->name, $method)) {
                continue;
            }

            foreach ($methodCall->getArgs() as $index => $arg) {
                if ($arg->name instanceof Identifier) {
                    if ($this->isName($arg->name, 'table')) {
                        return $arg;
                    }
                } elseif ($index === $position) {
                    return $arg;
                }
            }
        }

        return null;
    }

    private function resolvePivotClass(string $table): ?string
    {
        $classNames = array_unique([$this->studly($table), $this->studly($this->singularise($table))]);

        foreach ($this->modelNamespaces as $modelNamespace) {
            foreach ($classNames as $className) {
                $pivotClass = trim($modelNamespace, '\\') . '\\' . $className;

                if (! $this->reflectionProvider->hasClass($pivotClass)) {
                    continue;
                }

                $classReflection = $this->reflectionProvider->getClass($pivotClass);

                if (! $classReflection->is(self::MODEL_CLASS)) {
                    continue;
                }

                if ($this->resolveTableName($classReflection) === $table) {
                    return $pivotClass;
                }
            }
        }

        return null;
    }

    /**
     * Mirrors Illuminate\Database\Eloquent\Model::getTable().
     */
    private function resolveTableName(ClassReflection $classReflection): string
    {
        $nativeReflection = $classReflection->getNativeReflection();

        foreach ($nativeReflection->getAttributes(self::TABLE_ATTRIBUTE_CLASS) as $attributeReflection) {
            $arguments = $attributeReflection->getArguments();
            $name = $arguments['name'] ?? $arguments[0] ?? null;

            if (is_string($name)) {
                return $name;
            }
        }

        $table = $nativeReflection->getDefaultProperties()['table'] ?? null;

        if (is_string($table)) {
            return $table;
        }

        return $this->snake($this->pluralise($nativeReflection->getShortName()));
    }

    private function studly(string $value): string
    {
        return str_replace(' ', '', ucwords(str_replace(['-', '_'], ' ', $value)));
    }

    private function snake(string $value): string
    {
        return strtolower((string) preg_replace('/(?<!^)[A-Z]/', '_$0', $value));
    }

    private function singularise(string $word): string
    {
        $lower = strtolower($word);

        if (str_ends_with($lower, 'ies')) {
            return substr($word, 0, -3) . 'y';
        }

        if (preg_match('/(s|x|z|ch|sh)es$/', $lower) === 1) {
            return substr($word, 0, -2);
        }

        if (str_ends_with($lower, 's')) {
            return substr($word, 0, -1);
        }

        return $word;
    }

    private function pluralise(string $word): string
    {
        $lower = strtolower($word);

        if (str_ends_with($lower, 'y') && preg_match('/[aeiou]y$/', $lower) === 0) {
            return substr($word, 0, -1) . 'ies';
        }

        if (preg_match('/(s|x|z|ch|sh)$/', $lower) === 1) {
            return $word . 'es';
        }

        return $word . 's';
    }
}
