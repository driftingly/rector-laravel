<?php

namespace RectorLaravel\Tests\Rector\Class_\LivewireComponentComputedMethodToComputedAttributeRector\Fixture;

use Livewire\Component;

class SkipIfMethodAlreadyExists extends Component
{
    public function getUserProperty()
    {

    }

    public function user()
    {

    }
}

?>
