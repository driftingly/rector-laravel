<?php

namespace RectorLaravel\Tests\Rector\Class_\CommandPropertyToAsCommandAttributeRector\Fixture;

use Illuminate\Console\Command;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(name: 'mail:send', description: 'Send the queued emails')]
class ExistingAsCommandWithEmptyDescription extends Command
{
    protected $signature = 'mail:send';

    protected $description = '';
}

?>
-----
<?php

namespace RectorLaravel\Tests\Rector\Class_\CommandPropertyToAsCommandAttributeRector\Fixture;

use Illuminate\Console\Command;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(name: 'mail:send', description: 'Send the queued emails')]
class ExistingAsCommandWithEmptyDescription extends Command
{
}

?>
