<?php

namespace RectorLaravel\Tests\Rector\PropertyFetch\OptionalToNullsafeOperatorRector\Fixture;

/** @var \RectorLaravel\Tests\Rector\PropertyFetch\OptionalToNullsafeOperatorRector\Source\SomeClass|null $value */
optional($value, function () {
    throw new \RuntimeException(
        'The optional callback should not be called for null'
    );
})->something();
