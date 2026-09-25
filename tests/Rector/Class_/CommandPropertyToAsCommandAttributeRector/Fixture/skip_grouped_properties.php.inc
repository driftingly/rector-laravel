<?php

namespace RectorLaravel\Tests\Rector\Class_\CommandPropertyToAsCommandAttributeRector\Fixture;

use Illuminate\Console\Command;

class SkipGroupedProperties extends Command
{
    protected $signature = 'mail:send', $description = 'Send the queued emails';
}

?>
