<?php

namespace RectorLaravel\Tests\Rector\Class_\CommandPropertyToAsCommandAttributeRector\Fixture;

use Illuminate\Console\Command;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand('mail:send', 'Send emails')]
class ExistingAsCommandWithDescription extends Command
{
    protected $signature = 'mail:send';

    protected $description = 'Send the queued emails';
}

?>
-----
<?php

namespace RectorLaravel\Tests\Rector\Class_\CommandPropertyToAsCommandAttributeRector\Fixture;

use Illuminate\Console\Command;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand('mail:send', 'Send the queued emails')]
class ExistingAsCommandWithDescription extends Command
{
}

?>
