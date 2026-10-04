<?php

namespace RectorLaravel\Tests\Rector\Class_\CommandPropertyToAsCommandAttributeRector\Fixture;

use Illuminate\Console\Command;

class NamePropertyReadInClass extends Command
{
    protected $name = 'mail:send';

    public function handle(): void
    {
        $this->info($this->name);
    }
}

?>
-----
<?php

namespace RectorLaravel\Tests\Rector\Class_\CommandPropertyToAsCommandAttributeRector\Fixture;

use Illuminate\Console\Command;

#[\Symfony\Component\Console\Attribute\AsCommand(name: 'mail:send')]
class NamePropertyReadInClass extends Command
{
    public function handle(): void
    {
        $this->info($this->getName());
    }
}

?>
