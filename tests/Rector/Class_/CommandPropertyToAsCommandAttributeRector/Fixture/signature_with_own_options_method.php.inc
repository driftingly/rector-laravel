<?php

namespace RectorLaravel\Tests\Rector\Class_\CommandPropertyToAsCommandAttributeRector\Fixture;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class SignatureWithOwnOptionsMethod extends Command
{
    protected $signature = 'mail:send {user}';

    protected function getOptions(): array
    {
        return [['queue', null, InputOption::VALUE_NONE, 'Queue the mail']];
    }
}

?>
-----
<?php

namespace RectorLaravel\Tests\Rector\Class_\CommandPropertyToAsCommandAttributeRector\Fixture;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

#[\Symfony\Component\Console\Attribute\AsCommand(name: 'mail:send')]
class SignatureWithOwnOptionsMethod extends Command
{
    protected function getOptions(): array
    {
        return [['queue', null, InputOption::VALUE_NONE, 'Queue the mail']];
    }
    protected function getArguments(): array
    {
        return [
            new \Symfony\Component\Console\Input\InputArgument('user', \Symfony\Component\Console\Input\InputArgument::REQUIRED),
        ];
    }
}

?>
