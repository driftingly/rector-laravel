<?php

namespace RectorLaravel\Tests\Rector\Class_\LivewireComponentQueryStringToUrlAttributeRector\Fixture;

use Livewire\Component;

class DoesNotDoubleApplyAttributes extends Component
{
    #[\Livewire\Attributes\Url]
    public string $something = '';

    public string $another = '';

    protected $queryString = [
        'something',
        'another',
    ];
}

?>
-----
<?php

namespace RectorLaravel\Tests\Rector\Class_\LivewireComponentQueryStringToUrlAttributeRector\Fixture;

use Livewire\Component;

class DoesNotDoubleApplyAttributes extends Component
{
    #[\Livewire\Attributes\Url]
    public string $something = '';

    #[\Livewire\Attributes\Url]
    public string $another = '';
}

?>
