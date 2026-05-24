<?php

namespace RectorLaravel\Tests\Rector\Class_\CommandHiddenPropertyToHiddenAttributeRector\Fixture;

use Illuminate\Console\Command;

class SkipHiddenString extends Command
{
    protected $hidden = 'true';
}
