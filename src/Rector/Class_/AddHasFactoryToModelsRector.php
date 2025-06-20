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
use Rector\Rector\AbstractRector;
use Rector\Reflection\ReflectionResolver;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @changelog https://github.com/laravel/framework/pull/39310
 *
 * @see \RectorLaravel\Tests\Rector\Class_\RemoveModelPropertyFromFactoriesRector\RemoveModelPropertyFromFactoriesRectorTest
 */
final class AddHasFactoryToModelsRector extends AbstractRector implements ConfigurableRectorInterface
{
    private const string TRAIT_NAME = 'Illuminate\Database\Eloquent\Factories\HasFactory';

    /**
     * @var mixed[]
     */
    private array $allowList = [];

    public function __construct(
        private readonly ReflectionResolver $reflectionResolver,
    ) {}

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Adds the HasFactory trait to Models.', [
            new CodeSample(
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
CODE_SAMPLE
            ),
        ]);
    }

    public function configure(array $configuration): void
    {
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
            return null;
        }

        if ($this->allowList !== [] && ! $this->isNames($class, $this->allowList)) {
            return false;
        }

        $classReflection = $this->reflectionResolver->resolveClassReflection($class);
        if (! $classReflection instanceof ClassReflection) {
            return false;
        }

        return $classReflection->hasTraitUse(self::TRAIT_NAME);
    }
}
