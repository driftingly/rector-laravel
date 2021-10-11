<?php

namespace Rector\Laravel\Tests\Rector\PropertyFetch\OptionalToNullsafeOperatorRector\Fixture;

/** @var \Rector\Laravel\Tests\Rector\PropertyFetch\OptionalToNullsafeOperatorRector\Source\SomeClass|null $value */
optional($value)->present()->something();

?>
-----
<?php

namespace Rector\Laravel\Tests\Rector\PropertyFetch\OptionalToNullsafeOperatorRector\Fixture;

/** @var \Rector\Laravel\Tests\Rector\PropertyFetch\OptionalToNullsafeOperatorRector\Source\SomeClass|null $value */
optional($value)->present()->something();

?>
