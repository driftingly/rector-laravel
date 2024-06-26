<?php

namespace RectorLaravel\Tests\Rector\Class_\LivewireComponentQueryStringToUrlAttributeRector\Fixture;

use Livewire\Component;

class MyComponent extends Component
{
    public string $something = '';

    protected $queryString = [
        'something',
        'another',
    ];
}

?>
