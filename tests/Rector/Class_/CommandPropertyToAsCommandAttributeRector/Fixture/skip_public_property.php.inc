<?php

namespace RectorLaravel\Tests\Rector\Class_\CommandPropertyToAsCommandAttributeRector\Fixture;

use Illuminate\Console\Command;

class SkipPublicProperty extends Command
{
    public $signature = 'mail:send';
}

?>
