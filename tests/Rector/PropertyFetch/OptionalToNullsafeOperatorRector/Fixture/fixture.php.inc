<?php

namespace Rector\Laravel\Tests\Rector\PropertyFetch\OptionalToNullsafeOperatorRector\Fixture;

/** @var \Rector\Laravel\Tests\Rector\PropertyFetch\OptionalToNullsafeOperatorRector\Source\SomeClass|null $value */
optional($value)->something();
optional($value)->foo;
optional($value)->present()->something();
optional($value, function () {
    throw new \RuntimeException(
        'The optional callback should not be called for null'
    );
})->something();
optional(null)->something();
optional(5)->something();
optional(['present' => 'here'])->missing;
optional(['present' => 'here'])['present'];
optional((object) ['foo' => 'bar'])->foo;
optional(['foo' => 'bar'])->foo;
optional((object) ['foo' => 'bar'])->bar;

?>
-----
<?php

namespace Rector\Laravel\Tests\Rector\PropertyFetch\OptionalToNullsafeOperatorRector\Fixture;

/** @var \Rector\Laravel\Tests\Rector\PropertyFetch\OptionalToNullsafeOperatorRector\Source\SomeClass|null $value */
$value?->something();
$value?->foo;
optional($value)->present()->something();
optional($value, function () {
    throw new \RuntimeException(
        'The optional callback should not be called for null'
    );
})->something();
optional(null)->something();
optional(5)->something();
optional(['present' => 'here'])->missing;
optional(['present' => 'here'])['present'];
((object) ['foo' => 'bar'])?->foo;
optional(['foo' => 'bar'])->foo;
((object) ['foo' => 'bar'])?->bar;

?>
