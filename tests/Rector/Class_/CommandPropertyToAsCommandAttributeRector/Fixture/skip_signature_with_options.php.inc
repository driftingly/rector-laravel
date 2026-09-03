<?php

namespace RectorLaravel\Tests\Rector\Class_\CommandPropertyToAsCommandAttributeRector\Fixture;

use Illuminate\Console\Command;

class SkipSignatureWithOptions extends Command
{
    protected $signature = 'mail:send {--queue}';
}

?>
