<?php

namespace RectorLaravel\Tests\Rector\Class_\CommandPropertyToAsCommandAttributeRector\Fixture;

use Illuminate\Console\Command;

class SignatureWithOptions extends Command
{
    protected $signature = 'mail:send {--queue} {--subject=} {--body=hello} {--tags=*} {--cc=*a,b} {--Q|quiet}';
}

?>
-----
<?php

namespace RectorLaravel\Tests\Rector\Class_\CommandPropertyToAsCommandAttributeRector\Fixture;

use Illuminate\Console\Command;

#[\Symfony\Component\Console\Attribute\AsCommand(name: 'mail:send')]
class SignatureWithOptions extends Command
{
    protected function getOptions(): array
    {
        return [
            new \Symfony\Component\Console\Input\InputOption('queue', null, \Symfony\Component\Console\Input\InputOption::VALUE_NONE),
            new \Symfony\Component\Console\Input\InputOption('subject', null, \Symfony\Component\Console\Input\InputOption::VALUE_OPTIONAL),
            new \Symfony\Component\Console\Input\InputOption('body', null, \Symfony\Component\Console\Input\InputOption::VALUE_OPTIONAL, '', 'hello'),
            new \Symfony\Component\Console\Input\InputOption('tags', null, \Symfony\Component\Console\Input\InputOption::VALUE_OPTIONAL | \Symfony\Component\Console\Input\InputOption::VALUE_IS_ARRAY),
            new \Symfony\Component\Console\Input\InputOption('cc', null, \Symfony\Component\Console\Input\InputOption::VALUE_OPTIONAL | \Symfony\Component\Console\Input\InputOption::VALUE_IS_ARRAY, '', ['a', 'b']),
            new \Symfony\Component\Console\Input\InputOption('quiet', 'Q', \Symfony\Component\Console\Input\InputOption::VALUE_NONE),
        ];
    }
}

?>
