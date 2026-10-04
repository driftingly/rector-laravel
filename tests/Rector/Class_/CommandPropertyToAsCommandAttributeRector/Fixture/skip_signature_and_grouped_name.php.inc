<?php

namespace RectorLaravel\Tests\Rector\Class_\CommandPropertyToAsCommandAttributeRector\Fixture;

use Illuminate\Console\Command;

class SkipSignatureAndGroupedName extends Command
{
    protected $signature = 'mail:send';

    protected $name = 'mail:dispatch', $queue = 'emails';
}

?>
