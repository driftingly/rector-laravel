<?php

namespace RectorLaravel\Tests\Rector\Class_\CommandPropertyToAsCommandAttributeRector\Fixture;

use Illuminate\Console\Command;

class SkipSignatureAndName extends Command
{
    protected $signature = 'mail:send';

    protected $name = 'mail:dispatch';
}

?>
