<?php

namespace RectorLaravel\Tests\Rector\Class_\CommandPropertyToAsCommandAttributeRector\Fixture;

use Illuminate\Console\Command;

class SignatureWithArgumentsAndHandle extends Command
{
    protected $signature = 'mail:send {user} {--queue}';

    protected $description = 'Send the queued emails';

    public function handle(): void
    {
    }
}

?>
-----
<?php

namespace RectorLaravel\Tests\Rector\Class_\CommandPropertyToAsCommandAttributeRector\Fixture;

use Illuminate\Console\Command;

#[\Symfony\Component\Console\Attribute\AsCommand(name: 'mail:send', description: 'Send the queued emails')]
class SignatureWithArgumentsAndHandle extends Command
{
    public function handle(): void
    {
    }
    protected function getArguments(): array
    {
        return [
            new \Symfony\Component\Console\Input\InputArgument('user', \Symfony\Component\Console\Input\InputArgument::REQUIRED),
        ];
    }
    protected function getOptions(): array
    {
        return [
            new \Symfony\Component\Console\Input\InputOption('queue', null, \Symfony\Component\Console\Input\InputOption::VALUE_NONE),
        ];
    }
}

?>
