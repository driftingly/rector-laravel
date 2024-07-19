<?php

namespace RectorLaravel\Tests\Rector\Class_\LivewireComponentQueryStringToUrlAttributeRector\Fixture;

use Livewire\Component;

class MyComponent extends Component
{
    public const FOO_BAR = 'foo';

    public string $something = '';

    public string $another = '';

    public string $asKey = '';

    protected $queryString = [
        'something',
        'another',
        'asKey' => ['except' => 1, 'as' => 'foo'],
        self::FOO_BAR,
    ];
}

?>
-----
<?php

namespace RectorLaravel\Tests\Rector\Class_\LivewireComponentQueryStringToUrlAttributeRector\Fixture;

use Livewire\Component;

class MyComponent extends Component
{
    public const FOO_BAR = 'foo';

    #[\Livewire\Attributes\Url]
    public string $something = '';

    #[\Livewire\Attributes\Url]
    public string $another = '';

    #[\Livewire\Attributes\Url(except: 1, as: 'foo')]
    public string $asKey = '';

    protected $queryString = [
        self::FOO_BAR,
    ];
}

?>
