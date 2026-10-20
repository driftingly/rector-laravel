<?php

namespace RectorLaravel\Tests\Rector\Class_\CommandPropertyToAsCommandAttributeRector\Fixture;

use Illuminate\Console\Command;

class PropertyReadInClass extends Command
{
    protected $signature = 'mail:send';

    protected $description = 'Send the queued emails';

    public function handle(): void
    {
        echo $this->description;
        $this->info($this->name . ': ' . $this->description[0]);
    }
}

?>
-----
<?php

namespace RectorLaravel\Tests\Rector\Class_\CommandPropertyToAsCommandAttributeRector\Fixture;

use Illuminate\Console\Command;

#[\Symfony\Component\Console\Attribute\AsCommand(name: 'mail:send', description: 'Send the queued emails')]
class PropertyReadInClass extends Command
{
    public function handle(): void
    {
        echo $this->getDescription();
        $this->info($this->getName() . ': ' . $this->getDescription()[0]);
    }
}

?>
