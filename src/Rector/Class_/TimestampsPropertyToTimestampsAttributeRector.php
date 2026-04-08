<?php

declare(strict_types=1);

namespace RectorLaravel\Rector\Class_;

use PhpParser\Node;
use PhpParser\Node\Attribute;
use PhpParser\Node\AttributeGroup;
use PhpParser\Node\Expr\ConstFetch;
use PhpParser\Node\Name\FullyQualified;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\Property;
use PHPStan\Type\ObjectType;
use Rector\Php80\NodeAnalyzer\PhpAttributeAnalyzer;
use Rector\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

final class TimestampsPropertyToTimestampsAttributeRector extends AbstractRector
{
    public function __construct(
        private readonly PhpAttributeAnalyzer $phpAttributeAnalyzer,
    ) {}

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(
            'Replace $timestamps = false property with #[WithoutTimestamps] attribute in Eloquent Models',
            [
                new CodeSample(
                    <<<'PHP'
use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    public $timestamps = false;
}
PHP,
                    <<<'PHP'
use Illuminate\Database\Eloquent\Attributes\WithoutTimestamps;
use Illuminate\Database\Eloquent\Model;

#[WithoutTimestamps]
class User extends Model
{
}
PHP,
                ),
            ],
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
     * @param Class_ $node
     */
    public function refactor(Node $node): ?Node
    {
        if (!$this->isAnEloquentModelClass($node)) {
            return null;
        }

        if ($this->phpAttributeAnalyzer->hasPhpAttribute($node, 'Illuminate\Database\Eloquent\Attributes\WithoutTimestamps')) {
            return null;
        }

        $timestampsProperty = $node->getProperty('timestamps');
        if ($timestampsProperty === null){
            return null;
        }

        $this->removePropertyFromClass($node, $timestampsProperty);
        if($this->isFalseProperty($timestampsProperty)){
            $this->addWithoutTimestampsAttribute($node);
        }

        return $node;
    }

    private function isAnEloquentModelClass(Class_ $class): bool
    {
        if ($class->extends === null) {
            return false;
        }

        return $this->isName($class->extends, 'Illuminate\Database\Eloquent\Model') || $this->isName($class->extends, 'Model');
    }

    private function isFalseProperty(Property $timestampsProperty): bool
    {
        if (count($timestampsProperty->props) === 0) {
            return false;
        }

        $default = $timestampsProperty->props[0]->default;
        if ($default === null) {
            return false;
        }

        return $default instanceof ConstFetch && $this->isName($default->name, 'false');
    }

    private function removePropertyFromClass(Class_ $class, Property $timestampsProperty): void
    {
        foreach ($class->stmts as $key => $stmt) {
            if ($stmt === $timestampsProperty) {
                unset($class->stmts[$key]);
                break;
            }
        }
    }

    private function addWithoutTimestampsAttribute(Class_ $class): void
    {
        $class->attrGroups[] = new AttributeGroup([
            new Attribute(new FullyQualified('Illuminate\Database\Eloquent\Attributes\WithoutTimestamps')),
        ]);
    }
}
