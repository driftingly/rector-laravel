<?php

namespace RectorLaravel\Tests\Rector\Class_\LivewireComponentQueryStringToUrlAttributeRector\Fixture;

use Livewire\Component;

const FOOBAR = 'foobar';

class MyComponent extends Component
{
    public string $something = '';

    public string $another = '';

    protected $queryString = [
        'something',
        'another',
        FOOBAR,
    ];
}

?>
