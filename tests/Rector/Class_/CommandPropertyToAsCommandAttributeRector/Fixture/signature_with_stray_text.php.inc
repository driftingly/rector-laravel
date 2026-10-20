<?php

namespace RectorLaravel\Tests\Rector\Class_\CommandPropertyToAsCommandAttributeRector\Fixture;

use Illuminate\Console\Command;

class SignatureWithStrayText extends Command
{
    protected $signature = 'mail:send {user} unparsed';
}

?>
-----
<?php

namespace RectorLaravel\Tests\Rector\Class_\CommandPropertyToAsCommandAttributeRector\Fixture;

use Illuminate\Console\Command;

#[\Symfony\Component\Console\Attribute\AsCommand(name: 'mail:send')]
class SignatureWithStrayText extends Command
{
    protected function getArguments(): array
    {
        return [
            new \Symfony\Component\Console\Input\InputArgument('user', \Symfony\Component\Console\Input\InputArgument::REQUIRED),
        ];
    }
}

?>
