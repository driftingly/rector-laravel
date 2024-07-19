<?php

namespace RectorLaravel\Tests\Rector\Class_\LivewireComponentQueryStringToUrlAttributeRector\Fixture;

class SkipNonComponent
{
    public string $something = '';

    public string $another = '';

    protected $queryString = [
        'something',
        'another',
    ];
}

?>
