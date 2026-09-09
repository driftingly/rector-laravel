<?php

namespace RectorLaravel\Tests\Rector\Class_\CommandPropertyToAsCommandAttributeRector\Fixture;

use Illuminate\Console\Command;

class SkipNonStringSignature extends Command
{
    protected $signature = 'mail:' . 'send';

    protected $description = 'Send the queued emails';
}

?>
