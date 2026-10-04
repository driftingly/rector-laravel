<?php

namespace RectorLaravel\Tests\Rector\Class_\CommandPropertyToAsCommandAttributeRector\Fixture;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class SignatureArgumentsMergedIntoOwnMethod extends Command
{
    protected $signature = 'mail:send {user}';

    protected function getArguments(): array
    {
        return [
            new InputArgument('cc', InputArgument::OPTIONAL),
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
class SignatureArgumentsMergedIntoOwnMethod extends Command
{
    protected function getArguments(): array
    {
        return [
            new \Symfony\Component\Console\Input\InputArgument('user', \Symfony\Component\Console\Input\InputArgument::REQUIRED),
            new InputArgument('cc', InputArgument::OPTIONAL),
        ];
    }
}

?>
