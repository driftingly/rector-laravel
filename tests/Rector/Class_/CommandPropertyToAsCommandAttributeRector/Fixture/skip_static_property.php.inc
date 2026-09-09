<?php

namespace RectorLaravel\Tests\Rector\Class_\CommandPropertyToAsCommandAttributeRector\Fixture;

use Illuminate\Console\Command;

class SkipStaticProperty extends Command
{
    protected static $signature = 'mail:send';
}

?>
