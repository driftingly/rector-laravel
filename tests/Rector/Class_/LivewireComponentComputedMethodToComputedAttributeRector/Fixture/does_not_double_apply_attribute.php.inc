<?php

namespace RectorLaravel\Tests\Rector\Class_\LivewireComponentComputedMethodToComputedAttributeRector\Fixture;

use Livewire\Component;

class DoesNotDoubleApplyAttributes extends Component
{
    #[\Livewire\Attributes\Computed]
    public function getFooBarAttribute()
    {

    }

    public function getBarFooAttribute()
    {

    }
}

?>
-----
<?php

namespace RectorLaravel\Tests\Rector\Class_\LivewireComponentComputedMethodToComputedAttributeRector\Fixture;

use Livewire\Component;

class DoesNotDoubleApplyAttributes extends Component
{
    #[\Livewire\Attributes\Computed]
    public function getFooBarAttribute()
    {

    }

    #[\Livewire\Attributes\Computed]
    public function getBarFooAttribute()
    {

    }
}

?>
