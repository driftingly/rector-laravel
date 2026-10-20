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

// The signature keeps specifyParameters() from running, so the inherited
// getArguments() is dormant. Declaring an empty one shadows it.
class InheritedParameterMethods extends InheritedParameterMethodsCommand
{
    protected $signature = 'mail:send';
}

?>
-----
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

// The signature keeps specifyParameters() from running, so the inherited
// getArguments() is dormant. Declaring an empty one shadows it.
#[\Symfony\Component\Console\Attribute\AsCommand(name: 'mail:send')]
class InheritedParameterMethods extends InheritedParameterMethodsCommand
{
    protected function getArguments(): array
    {
        return [];
    }
}

?>
