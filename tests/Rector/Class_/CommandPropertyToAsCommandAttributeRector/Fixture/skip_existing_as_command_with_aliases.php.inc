<?php

namespace RectorLaravel\Tests\Rector\Class_\CommandPropertyToAsCommandAttributeRector\Fixture;

use Illuminate\Console\Command;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(name: 'mail:send', aliases: ['email:send'])]
class SkipExistingAsCommandWithAliases extends Command
{
    protected $signature = 'mail:send';
}

?>
