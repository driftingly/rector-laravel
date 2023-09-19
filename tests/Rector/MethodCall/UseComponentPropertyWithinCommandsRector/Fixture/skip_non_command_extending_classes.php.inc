<?php

namespace RectorLaravel\Tests\Rector\MethodCall\UseComponentPropertyWithinCommandsRector\Fixture;

class CommandWithComponents extends Command
{
    public function handle()
    {
        $this->ask('What is your name?');
    }
}

?>
-----
<?php

namespace RectorLaravel\Tests\Rector\MethodCall\UseComponentPropertyWithinCommandsRector\Fixture;

class CommandWithComponents extends Command
{
    public function handle()
    {
        $this->ask('What is your name?');
    }
}

?>
