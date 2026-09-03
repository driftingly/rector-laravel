<?php

namespace RectorLaravel\Tests\Rector\Class_\CommandPropertyToAsCommandAttributeRector\Fixture;

use Illuminate\Console\Command;

abstract class InheritedParameterMethodsCommand extends Command
{
    protected function getArguments(): array
    {
        return [['user', null, 'The user']];
    }
}

class SkipInheritedParameterMethods extends InheritedParameterMethodsCommand
{
    protected $signature = 'mail:send';
}

?>
