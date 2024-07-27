<?php

namespace RectorLaravel\Tests\Rector\Class_\LivewireComponentComputedMethodToComputedAttributeRector\Fixture;

use Livewire\Component;

class SkipIfMethodAlreadyExists extends Component
{
    public function getUserAttribute()
    {

    }

    public function user()
    {

    }
}

?>
