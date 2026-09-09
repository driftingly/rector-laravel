<?php

namespace RectorLaravel\Tests\Rector\Class_\CommandPropertyToAsCommandAttributeRector\Fixture;

use Illuminate\Console\Command;

class SignatureWithDescriptions extends Command
{
    protected $signature = 'mail:send {user : The user ID} {--queue : Queue the mail} {--tries=3 : How many tries}';

    protected $description = 'Send the queued emails';
}

?>
-----
<?php

namespace RectorLaravel\Tests\Rector\Class_\CommandPropertyToAsCommandAttributeRector\Fixture;

use Illuminate\Console\Command;

#[\Symfony\Component\Console\Attribute\AsCommand(name: 'mail:send', description: 'Send the queued emails')]
class SignatureWithDescriptions extends Command
{
    protected function getArguments(): array
    {
        return [
            new \Symfony\Component\Console\Input\InputArgument('user', \Symfony\Component\Console\Input\InputArgument::REQUIRED, 'The user ID'),
        ];
    }

    protected function getOptions(): array
    {
        return [
            new \Symfony\Component\Console\Input\InputOption('queue', null, \Symfony\Component\Console\Input\InputOption::VALUE_NONE, 'Queue the mail'),
            new \Symfony\Component\Console\Input\InputOption('tries', null, \Symfony\Component\Console\Input\InputOption::VALUE_OPTIONAL, 'How many tries', '3'),
        ];
    }
}

?>
