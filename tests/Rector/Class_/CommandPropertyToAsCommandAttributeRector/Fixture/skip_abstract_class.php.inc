<?php

namespace RectorLaravel\Tests\Rector\Class_\CommandPropertyToAsCommandAttributeRector\Fixture;

use Illuminate\Console\Command;

abstract class SkipAbstractClass extends Command
{
    protected $signature = 'mail:send';

    protected $description = 'Send the queued emails';
}

?>
