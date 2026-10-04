<?php

namespace RectorLaravel\Tests\Rector\Class_\CommandPropertyToAsCommandAttributeRector\Fixture;

use Illuminate\Console\Command;

class NamePropertyWithParameterMethods extends Command
{
    protected $name = 'mail:send';

    protected $description = 'Send the queued emails';

    protected function getArguments(): array
    {
        return [['user', null, 'The user']];
    }
}

?>
-----
<?php

namespace RectorLaravel\Tests\Rector\Class_\CommandPropertyToAsCommandAttributeRector\Fixture;

use Illuminate\Console\Command;

#[\Symfony\Component\Console\Attribute\AsCommand(name: 'mail:send', description: 'Send the queued emails')]
class NamePropertyWithParameterMethods extends Command
{
    protected function getArguments(): array
    {
        return [['user', null, 'The user']];
    }
}

?>
