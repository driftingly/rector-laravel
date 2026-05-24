<?php

namespace RectorLaravel\Tests\Rector\Class_\CommandHiddenPropertyToHiddenAttributeRector\Fixture;

use Illuminate\Console\Command;

#[\Illuminate\Console\Attributes\Hidden]
class SkipExistingAttribute extends Command
{
    protected $hidden = true;
}

?>
