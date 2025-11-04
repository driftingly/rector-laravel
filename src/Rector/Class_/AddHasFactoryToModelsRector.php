<?php

declare(strict_types=1);

namespace RectorLaravel\Rector\Class_;

use PhpParser\Node;
use PhpParser\Node\Name\FullyQualified;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\TraitUse;
use PHPStan\Reflection\ClassReflection;
use PHPStan\Type\ObjectType;
use Rector\Contract\Rector\ConfigurableRectorInterface;
use Rector\Reflection\ReflectionResolver;
use RectorLaravel\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\ConfiguredCodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;
use Webmozart\Assert\Assert;

/**
 * @changelog https://github.com/laravel/framework/pull/39310
 *
 * @see \RectorLaravel\Tests\Rector\Class_\AddHasFactoryToModelsRector\AddHasFactoryToModelsRectorTest
 * @see \RectorLaravel\Tests\Rector\Class_\AddHasFactoryToModelsRector\AddHasFactoryToModelsRectorConfiguredTest
 */
final class AddHasFactoryToModelsRector extends AbstractRector implements ConfigurableRectorInterface
{
    /**
     * @readonly
     */
    private ReflectionResolver $reflectionResolver;
    /**
     * @var string
     */
    private const TRAIT_NAME = 'Illuminate\Database\Eloquent\Factories\HasFactory';

    /**
     * @var string[]
     */
    private array $allowList = [];

    public function __construct(ReflectionResolver $reflectionResolver)
    {
        $this->reflectionResolver = $reflectionResolver;
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Adds the HasFactory trait to Models.', [
            new ConfiguredCodeSample(
                <<<'CODE_SAMPLE'
use Illuminate\Database\Eloquent\Model;

class User extends Model
{
}
CODE_SAMPLE

                ,
                <<<'CODE_SAMPLE'
use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    use \Illuminate\Database\Eloquent\Factories\HasFactory;
}
CODE_SAMPLE, ['App\Models\User']
            ),
        ]);
    }

    public function configure(array $configuration): void
    {
        Assert::allString($configuration);
        $this->allowList = $configuration;
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
        if ($this->shouldSkipClass($node)) {
            return null;
        }

        $traitUse = new TraitUse([new FullyQualified(self::TRAIT_NAME)]);

        $node->stmts = array_merge([$traitUse], $node->stmts);

        return $node;
    }

    private function shouldSkipClass(Class_ $class): bool
    {
        if (! $this->isObjectType($class, new ObjectType('Illuminate\Database\Eloquent\Model'))) {
            return true;
        }

        if ($this->allowList !== [] && ! $this->isNames($class, $this->allowList)) {
            return true;
        }

        $classReflection = $this->reflectionResolver->resolveClassReflection($class);
        if (! $classReflection instanceof ClassReflection) {
            return true;
        }

        return $classReflection->hasTraitUse(self::TRAIT_NAME);
    }
}
