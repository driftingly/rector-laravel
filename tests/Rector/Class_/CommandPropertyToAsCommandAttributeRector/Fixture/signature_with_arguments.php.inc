<?php

namespace RectorLaravel\Tests\Rector\Class_\CommandPropertyToAsCommandAttributeRector\Fixture;

use Illuminate\Console\Command;

class SignatureWithArguments extends Command
{
    protected $signature = 'mail:send {user} {subject?} {body=hello} {tags=*a,b} {cc*} {bcc?*}';
}

?>
-----
<?php

namespace RectorLaravel\Tests\Rector\Class_\CommandPropertyToAsCommandAttributeRector\Fixture;

use Illuminate\Console\Command;

#[\Symfony\Component\Console\Attribute\AsCommand(name: 'mail:send')]
class SignatureWithArguments extends Command
{
    protected function getArguments(): array
    {
        return [
            new \Symfony\Component\Console\Input\InputArgument('user', \Symfony\Component\Console\Input\InputArgument::REQUIRED),
            new \Symfony\Component\Console\Input\InputArgument('subject', \Symfony\Component\Console\Input\InputArgument::OPTIONAL),
            new \Symfony\Component\Console\Input\InputArgument('body', \Symfony\Component\Console\Input\InputArgument::OPTIONAL, '', 'hello'),
            new \Symfony\Component\Console\Input\InputArgument('tags', \Symfony\Component\Console\Input\InputArgument::IS_ARRAY, '', ['a', 'b']),
            new \Symfony\Component\Console\Input\InputArgument('cc', \Symfony\Component\Console\Input\InputArgument::IS_ARRAY | \Symfony\Component\Console\Input\InputArgument::REQUIRED),
            new \Symfony\Component\Console\Input\InputArgument('bcc', \Symfony\Component\Console\Input\InputArgument::IS_ARRAY),
        ];
    }
}

?>
