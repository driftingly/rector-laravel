<?php

namespace Rector\Laravel\Tests\Rector\PropertyFetch\OptionalToNullsafeOperatorRector\Fixture;

/** @var \Rector\Laravel\Tests\Rector\PropertyFetch\OptionalToNullsafeOperatorRector\Source\SomeClass|null $value */
optional($value)->something();
optional($value)->something(true);
optional($value)->foo;
optional(request()->user())->getKey();

?>
-----
<?php

namespace Rector\Laravel\Tests\Rector\PropertyFetch\OptionalToNullsafeOperatorRector\Fixture;

/** @var \Rector\Laravel\Tests\Rector\PropertyFetch\OptionalToNullsafeOperatorRector\Source\SomeClass|null $value */
$value?->something();
$value?->something(true);
$value?->foo;
request()->user()?->getKey();

?>
