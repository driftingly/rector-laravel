<?php

namespace RectorLaravel\Tests\Sets\Livewire30;

use Livewire\Component;

class FixtureComponent extends Component
{
    #[\Livewire\Attributes\Rule]
    public $email;

    public function getFooBarProperty()
    {
    }
}

?>
-----
<?php

namespace RectorLaravel\Tests\Sets\Livewire30;

use Livewire\Component;

class FixtureComponent extends Component
{
    #[\Livewire\Attributes\Validate]
    public $email;

    #[\Livewire\Attributes\Computed]
    public function fooBar()
    {
    }
}

?>
