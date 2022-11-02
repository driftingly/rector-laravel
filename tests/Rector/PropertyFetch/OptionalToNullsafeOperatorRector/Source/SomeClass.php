<?php

namespace RectorLaravel\Tests\Rector\PropertyFetch\OptionalToNullsafeOperatorRector\Source;

class SomeClass
{
    public int $foo;

    public function something(bool $parameter = false): void
    {

    }
}
