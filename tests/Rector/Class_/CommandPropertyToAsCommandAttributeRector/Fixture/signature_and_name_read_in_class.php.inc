<?php

namespace RectorLaravel\Tests\Rector\Class_\CommandPropertyToAsCommandAttributeRector\Fixture;

use Illuminate\Console\Command;

class SignatureAndNameReadInClass extends Command
{
    protected $signature = 'mail:send';

    protected $name = 'mail:dispatch';

    public function handle(): void
    {
        echo $this->name;
    }
}

?>
-----
<?php

namespace RectorLaravel\Tests\Rector\Class_\CommandPropertyToAsCommandAttributeRector\Fixture;

use Illuminate\Console\Command;

#[\Symfony\Component\Console\Attribute\AsCommand(name: 'mail:send')]
class SignatureAndNameReadInClass extends Command
{
    public function handle(): void
    {
        echo $this->getName();
    }
}

?>
