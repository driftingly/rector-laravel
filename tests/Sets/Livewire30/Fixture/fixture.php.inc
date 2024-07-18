<?php

namespace RectorLaravel\Tests\Sets\Livewire30;

use Livewire\Component;

class FixtureComponent extends Component
{
    #[\Livewire\Attributes\Rule]
    public $email;
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
}

?>
