<?php

namespace RectorLaravel\Tests\Rector\Class_\CommandPropertyToAsCommandAttributeRector\Fixture;

use Illuminate\Console\Command;

class SkipSignatureWithBraceInName extends Command
{
    protected $signature = 'mail:send{user}';
}

?>
