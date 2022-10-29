<?php

namespace RectorLaravel\Tests\Rector\PropertyFetch\OptionalToNullsafeOperatorRector\Fixture;

/** @var \RectorLaravel\Tests\Rector\PropertyFetch\OptionalToNullsafeOperatorRector\Source\SomeClass|null $value */
optional($value)->present()->something();

?>
-----
<?php

namespace RectorLaravel\Tests\Rector\PropertyFetch\OptionalToNullsafeOperatorRector\Fixture;

/** @var \RectorLaravel\Tests\Rector\PropertyFetch\OptionalToNullsafeOperatorRector\Source\SomeClass|null $value */
optional($value)->present()->something();

?>
