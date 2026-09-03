<?php

namespace RectorLaravel\Tests\Rector\Class_\CommandPropertyToAsCommandAttributeRector\Fixture;

use Illuminate\Console\Command;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(name: 'mail:send')]
class SkipExistingAsCommand extends Command
{
    protected $signature = 'mail:send';

    protected $description = 'Send the queued emails';
}

?>
