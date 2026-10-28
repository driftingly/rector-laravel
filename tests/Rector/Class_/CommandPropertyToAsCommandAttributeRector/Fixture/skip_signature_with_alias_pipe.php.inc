<?php

namespace RectorLaravel\Tests\Rector\Class_\CommandPropertyToAsCommandAttributeRector\Fixture;

use Illuminate\Console\Command;

class SkipSignatureWithAliasPipe extends Command
{
    protected $signature = 'mail:send|email:send';
}

?>
