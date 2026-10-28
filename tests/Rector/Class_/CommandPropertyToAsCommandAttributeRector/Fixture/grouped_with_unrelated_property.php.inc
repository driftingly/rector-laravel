<?php

namespace RectorLaravel\Tests\Rector\Class_\CommandPropertyToAsCommandAttributeRector\Fixture;

use Illuminate\Console\Command;

class GroupedWithUnrelatedProperty extends Command
{
    protected $queue = 'emails', $signature = 'mail:send {user}', $connection = 'redis';
}

?>
-----
<?php

namespace RectorLaravel\Tests\Rector\Class_\CommandPropertyToAsCommandAttributeRector\Fixture;

use Illuminate\Console\Command;

#[\Symfony\Component\Console\Attribute\AsCommand(name: 'mail:send')]
class GroupedWithUnrelatedProperty extends Command
{
    protected $queue = 'emails', $connection = 'redis';
    protected function getArguments(): array
    {
        return [
            new \Symfony\Component\Console\Input\InputArgument('user', \Symfony\Component\Console\Input\InputArgument::REQUIRED),
        ];
    }
}

?>
