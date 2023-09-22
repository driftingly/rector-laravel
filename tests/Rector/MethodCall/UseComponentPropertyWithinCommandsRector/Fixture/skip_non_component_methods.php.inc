<?php

namespace RectorLaravel\Tests\Rector\MethodCall\UseComponentPropertyWithinCommandsRector\Fixture;

use Illuminate\Console\Command;

class CommandWithComponents extends Command
{
    public function handle()
    {
        $this->test();
    }
}

?>
-----
<?php

namespace RectorLaravel\Tests\Rector\MethodCall\UseComponentPropertyWithinCommandsRector\Fixture;

use Illuminate\Console\Command;

class CommandWithComponents extends Command
{
    public function handle()
    {
        $this->test();
    }
}

?>
