<?php

namespace RectorLaravel\Rector\Class_;

use PhpParser\Node;
use PhpParser\Node\Name\FullyQualified;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassMethod;
use PHPStan\Type\ObjectType;
use Rector\Exception\ShouldNotHappenException;
use RectorLaravel\AbstractRector;
use RectorLaravel\NodeAnalyzer\ModelAnalyzer;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

final class AddReturnTypeToModelRelationshipMethodRector extends AbstractRector
{
    public function __construct(private readonly ModelAnalyzer $modelAnalyzer) {}

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Add return type to relationship methods in Models', [
            new CodeSample(<<<'CODE_SAMPLE'
class User extends Model
{
    public function posts()
    {
        return $this->hasMany(Post::class);
    }
}
CODE_SAMPLE,
                <<<'CODE_SAMPLE'
class User extends Model
{
    public function posts(): HasMany
    {
        return $this->hasMany(Post::class);
    }
}
CODE_SAMPLE
            ),
        ]);
    }

    public function getNodeTypes(): array
    {
        return [Class_::class];
    }

    /**
     * @param  Class_  $node
     */
    public function refactor(Node $node): ?Class_
    {
        // Check if it is a Model
        if (! $this->isObjectType($node, new ObjectType('Illuminate\Database\Eloquent\Model'))) {

            return null;
        }

        $hasChanged = false;

        // Goes through each method to find relationship methods without a return type
        foreach ($node->stmts as $stmt) {
            if ($stmt instanceof ClassMethod) {
                if ($stmt->isAbstract() || ! $stmt->isPublic() || $stmt->returnType instanceof Node) {
                    continue;
                }

                $relationMethodUsed = $this->modelAnalyzer->classMethodReturnsRelationship($stmt);

                if ($relationMethodUsed === null) {
                    continue;
                }

                $classType = match ($relationMethodUsed) {
                    'hasOne' => 'Illuminate\Database\Eloquent\Relations\HasOne',
                    'hasMany' => 'Illuminate\Database\Eloquent\Relations\HasMany',
                    'belongsTo' => 'Illuminate\Database\Eloquent\Relations\BelongsTo',
                    'belongsToMany' => 'Illuminate\Database\Eloquent\Relations\BelongsToMany',
                    'morphTo' => 'Illuminate\Database\Eloquent\Relations\MorphTo',
                    'morphOne' => 'Illuminate\Database\Eloquent\Relations\MorphOne',
                    'morphMany' => 'Illuminate\Database\Eloquent\Relations\MorphMany',
                    'morphToMany' => 'Illuminate\Database\Eloquent\Relations\MorphToMany',
                    default => throw new ShouldNotHappenException('Unknown relationship method used')
                };

                // set the return type of the method
                $stmt->returnType = new FullyQualified($classType);

                $hasChanged = true;
            }
        }

        if ($hasChanged) {
            return $node;
        }

        return null;
    }
}
