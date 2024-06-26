<?php

namespace RectorLaravel\Tests\Rector\Class_\LivewireComponentQueryStringToUrlAttributeRector\Fixture;

use Livewire\Component;

class MyComponent extends Component
{
    public string $something = '';

    public string $another = '';

    public string $asKey = '';

    protected $queryString = [
        'something',
        'another',
        'asKey' => ['except' => 1],
    ];
}

?>
-----
<?php

namespace RectorLaravel\Tests\Rector\Class_\LivewireComponentQueryStringToUrlAttributeRector\Fixture;

use Livewire\Component;

class MyComponent extends Component
{
    #[\Livewire\Attributes\Url]
    public string $something = '';

    #[\Livewire\Attributes\Url]
    public string $another = '';

    #[\Livewire\Attributes\Url]
    public string $asKey = '';
}

?>
