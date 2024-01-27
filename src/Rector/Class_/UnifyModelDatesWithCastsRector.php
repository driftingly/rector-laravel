<?php

declare(strict_types=1);

namespace RectorLaravel\Rector\Class_;

use PhpParser\Builder\Property as PropertyBuilder;
use PhpParser\Node;
use PhpParser\Node\Expr\Array_;
use PhpParser\Node\Expr\ArrayItem;
use PhpParser\Node\Scalar\String_;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\Property;
use PHPStan\Type\ObjectType;
use Rector\BetterPhpDocParser\PhpDocInfo\PhpDocInfoFactory;
use Rector\NodeManipulator\ClassInsertManipulator;
use Rector\PhpParser\Node\Value\ValueResolver;
use Rector\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;
use Webmozart\Assert\Assert;

/**
 * @changelog https://github.com/laravel/framework/pull/32856
 *
 * @see \RectorLaravel\Tests\Rector\Class_\UnifyModelDatesWithCastsRector\UnifyModelDatesWithCastsRectorTest
 */
final class UnifyModelDatesWithCastsRector extends AbstractRector
{
    /**
     * @readonly
     * @var \Rector\NodeManipulator\ClassInsertManipulator
     */
    private $classInsertManipulator;
    /**
     * @readonly
     * @var \Rector\PhpParser\Node\Value\ValueResolver
     */
    private $valueResolver;
    /**
     * @readonly
     * @var \Rector\BetterPhpDocParser\PhpDocInfo\PhpDocInfoFactory
     */
    private $phpDocInfoFactory;
    public function __construct(ClassInsertManipulator $classInsertManipulator, ValueResolver $valueResolver, PhpDocInfoFactory $phpDocInfoFactory)
    {
        $this->classInsertManipulator = $classInsertManipulator;
        $this->valueResolver = $valueResolver;
        $this->phpDocInfoFactory = $phpDocInfoFactory;
    }
    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Unify Model $dates property with $casts', [
            new CodeSample(
                <<<'CODE_SAMPLE'
use Illuminate\Database\Eloquent\Model;

class Person extends Model
{
    protected $casts = [
        'age' => 'integer',
    ];

    protected $dates = ['birthday'];
}
CODE_SAMPLE

                ,
                <<<'CODE_SAMPLE'
use Illuminate\Database\Eloquent\Model;

class Person extends Model
{
    protected $casts = [
        'age' => 'integer', 'birthday' => 'datetime',
    ];
}
CODE_SAMPLE
            ),
        ]);
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
    public function refactor(Node $node)
    {
        if (! $this->isObjectType($node, new ObjectType('Illuminate\Database\Eloquent\Model'))) {
            return null;
        }

        $datesProperty = $node->getProperty('dates');

        if (! $datesProperty instanceof Property) {
            return null;
        }

        $datesPropertyProperty = $datesProperty->props[0];
        if (! $datesPropertyProperty->default instanceof Array_) {
            return null;
        }

        $dates = $this->valueResolver->getValue($datesPropertyProperty->default);
        if (! is_array($dates)) {
            return null;
        }

        if ($dates === []) {
            return null;
        }

        $castsProperty = $node->getProperty('casts');

        // add property $casts if not exists
        if (! $castsProperty instanceof Property) {
            $castsProperty = $this->createCastsProperty();
            $this->classInsertManipulator->addAsFirstMethod($node, $castsProperty);
        }

        $castsPropertyProperty = $castsProperty->props[0];
        if (! $castsPropertyProperty->default instanceof Array_) {
            return null;
        }

        $casts = $this->valueResolver->getValue($castsPropertyProperty->default);
        // exclude attributes added in $casts
        $missingDates = array_diff($dates, array_keys($casts));
        Assert::allString($missingDates);

        foreach ($missingDates as $missingDate) {
            $castsPropertyProperty->default->items[] = new ArrayItem(
                new String_('datetime'),
                new String_($missingDate)
            );
        }

        unset($node->stmts[array_search($datesProperty, $node->stmts, true)]);

        return null;
    }

    private function createCastsProperty(): Property
    {
        $propertyBuilder = new PropertyBuilder('casts');
        $propertyBuilder->makeProtected();
        $propertyBuilder->setDefault([]);

        $property = $propertyBuilder->getNode();

        $this->phpDocInfoFactory->createFromNode($property);

        return $property;
    }
}
