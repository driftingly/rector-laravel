<?php

namespace RectorLaravel\Tests\Rector\Class_\CommandPropertyToAsCommandAttributeRector\Fixture;

use Illuminate\Console\Command;

class SkipSignatureWithArguments extends Command
{
    protected $signature = 'mail:send {user}';

    protected $description = 'Send the queued emails';
}

?>
