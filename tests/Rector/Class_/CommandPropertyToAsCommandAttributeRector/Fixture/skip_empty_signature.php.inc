<?php

namespace RectorLaravel\Tests\Rector\Class_\CommandPropertyToAsCommandAttributeRector\Fixture;

use Illuminate\Console\Command;

class SkipEmptySignature extends Command
{
    protected $signature = '';

    protected $description = 'Send the queued emails';
}

?>
