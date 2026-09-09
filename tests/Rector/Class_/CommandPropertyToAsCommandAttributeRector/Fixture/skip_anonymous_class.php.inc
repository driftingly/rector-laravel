<?php

namespace RectorLaravel\Tests\Rector\Class_\CommandPropertyToAsCommandAttributeRector\Fixture;

use Illuminate\Console\Command;

function makeCommand(): Command
{
    return new class extends Command {
        protected $signature = 'mail:send';

        protected $description = 'Send the queued emails';
    };
}

?>
