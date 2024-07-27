<?php

namespace RectorLaravel\Tests\Rector\Class_\LivewireComponentComputedMethodToComputedAttributeRector\Fixture;

use Livewire\Component;

class MyComponent extends Component
{
    public function getFooBarProperty()
    {

    }

    public function getBarFooProperty()
    {

    }
}

?>
-----
<?php

namespace RectorLaravel\Tests\Rector\Class_\LivewireComponentComputedMethodToComputedAttributeRector\Fixture;

use Livewire\Component;

class MyComponent extends Component
{
    #[\Livewire\Attributes\Computed]
    public function fooBar()
    {

    }

    #[\Livewire\Attributes\Computed]
    public function barFoo()
    {

    }
}

?>
