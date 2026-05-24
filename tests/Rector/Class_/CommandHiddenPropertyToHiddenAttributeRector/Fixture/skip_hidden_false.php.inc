<?php

namespace RectorLaravel\Tests\Rector\Class_\CommandHiddenPropertyToHiddenAttributeRector\Fixture;

use Illuminate\Console\Command;

class SkipHiddenFalse extends Command
{
    protected $hidden = false;
}

?>
