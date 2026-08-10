<?php

declare(strict_types=1);

namespace RectorLaravel\Rector\MethodCall;

use Illuminate\Support\Str;
use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr\ClassConstFetch;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Name\FullyQualified;
use PhpParser\Node\Scalar\String_;
use PHPStan\Reflection\ReflectionProvider;
use PHPStan\Type\ObjectType;
use Rector\Contract\Rector\ConfigurableRectorInterface;
use RectorLaravel\AbstractRector;
use RectorLaravel\NodeAnalyzer\ModelAnalyzer;
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

    /**
     * @var list<string>
     */
    private array $modelNamespaces = ['App\Models'];

    public function __construct(private readonly ModelAnalyzer $modelAnalyzer, private readonly ReflectionProvider $reflectionProvider) {}

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
        $tablePosition = match (true) {
            $this->isName($node->name, 'belongsToMany') => 1,
            $this->isName($node->name, 'morphToMany'), $this->isName($node->name, 'morphedByMany') => 2,
            default => null,
        };

        if ($tablePosition === null) {
            return null;
        }

        $tableArgument = $node->getArg('table', $tablePosition);

        if (! $tableArgument instanceof Arg) {
            return null;
        }

        if (! $this->isObjectType($node->var, new ObjectType('Illuminate\Database\Eloquent\Model'))) {
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

    private function resolvePivotClass(string $table): ?string
    {
        $classNames = array_unique([Str::studly($table), Str::studly(Str::singular($table))]);

        foreach ($this->modelNamespaces as $modelNamespace) {
            foreach ($classNames as $className) {
                $pivotClass = trim($modelNamespace, '\\') . '\\' . $className;

                if (! $this->reflectionProvider->hasClass($pivotClass)) {
                    continue;
                }

                $classReflection = $this->reflectionProvider->getClass($pivotClass);

                if (! $classReflection->is('Illuminate\Database\Eloquent\Model')) {
                    continue;
                }

                if ($this->modelAnalyzer->getTable(new ObjectType($pivotClass)) === $table) {
                    return $pivotClass;
                }
            }
        }

        return null;
    }
}
