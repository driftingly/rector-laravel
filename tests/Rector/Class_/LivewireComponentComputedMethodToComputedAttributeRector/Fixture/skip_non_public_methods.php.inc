<?php

namespace RectorLaravel\Tests\Rector\Class_\LivewireComponentComputedMethodToComputedAttributeRector\Fixture;

use Livewire\Component;

class MyComponent extends Component
{
    private function getFooBarProperty()
    {

    }

    protected function getBarFooProperty()
    {

    }
}

?>
