<?php

namespace RectorLaravel\Tests\Rector\Class_\CommandPropertyToAsCommandAttributeRector\Fixture;

use Illuminate\Console\Command;

abstract class GeneratorLikeCommand extends Command
{
    protected function getArguments(): array
    {
        return [['name', null, 'The name']];
    }

    protected function getOptions(): array
    {
        return [['force', null, null, 'Overwrite']];
    }
}

class InheritedParameterMethodsWithArguments extends GeneratorLikeCommand
{
    protected $signature = 'make:thing {user}';
}

?>
-----
<?php

namespace RectorLaravel\Tests\Rector\Class_\CommandPropertyToAsCommandAttributeRector\Fixture;

use Illuminate\Console\Command;

abstract class GeneratorLikeCommand extends Command
{
    protected function getArguments(): array
    {
        return [['name', null, 'The name']];
    }

    protected function getOptions(): array
    {
        return [['force', null, null, 'Overwrite']];
    }
}

#[\Symfony\Component\Console\Attribute\AsCommand(name: 'make:thing')]
class InheritedParameterMethodsWithArguments extends GeneratorLikeCommand
{
    protected function getArguments(): array
    {
        return [
            new \Symfony\Component\Console\Input\InputArgument('user', \Symfony\Component\Console\Input\InputArgument::REQUIRED),
        ];
    }
    protected function getOptions(): array
    {
        return [];
    }
}

?>
