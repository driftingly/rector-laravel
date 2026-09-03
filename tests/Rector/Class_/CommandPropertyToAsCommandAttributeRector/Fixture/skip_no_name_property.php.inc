<?php

namespace RectorLaravel\Tests\Rector\Class_\CommandPropertyToAsCommandAttributeRector\Fixture;

use Illuminate\Console\Command;

class SkipNoNameProperty extends Command
{
    protected $description = 'Send the queued emails';
}

?>
