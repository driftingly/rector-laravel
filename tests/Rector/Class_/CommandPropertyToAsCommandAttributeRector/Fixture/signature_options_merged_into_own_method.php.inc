<?php

namespace RectorLaravel\Tests\Rector\Class_\CommandPropertyToAsCommandAttributeRector\Fixture;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class SignatureOptionsMergedIntoOwnMethod extends Command
{
    protected $signature = 'mail:send {--queue}';

    protected function getOptions(): array
    {
        return [
            ['delay', 'd', InputOption::VALUE_REQUIRED, 'Delay in seconds'],
        ];
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
class SignatureOptionsMergedIntoOwnMethod extends Command
{
    protected function getOptions(): array
    {
        return [
            new \Symfony\Component\Console\Input\InputOption('queue', null, \Symfony\Component\Console\Input\InputOption::VALUE_NONE),
            ['delay', 'd', InputOption::VALUE_REQUIRED, 'Delay in seconds'],
        ];
    }
}

?>
