<?php

namespace RectorLaravel\Tests\Rector\Class_\CommandPropertyToAsCommandAttributeRector\Fixture;

use Illuminate\Console\Command;

class SignatureAndGroupedName extends Command
{
    protected $signature = 'mail:send';

    protected $name = 'mail:dispatch', $queue = 'emails';
}

?>
-----
<?php

namespace RectorLaravel\Tests\Rector\Class_\CommandPropertyToAsCommandAttributeRector\Fixture;

use Illuminate\Console\Command;

#[\Symfony\Component\Console\Attribute\AsCommand(name: 'mail:send')]
class SignatureAndGroupedName extends Command
{
    protected $queue = 'emails';
}

?>
