<?php

namespace RectorLaravel\Tests\Rector\Class_\CommandPropertyToAsCommandAttributeRector\Fixture;

use Illuminate\Console\Command;

class ConstantExpressionSignature extends Command
{
    protected $signature = 'mail:' . 'send';

    protected $description = 'Send the queued emails';
}

?>
-----
<?php

namespace RectorLaravel\Tests\Rector\Class_\CommandPropertyToAsCommandAttributeRector\Fixture;

use Illuminate\Console\Command;

#[\Symfony\Component\Console\Attribute\AsCommand(name: 'mail:send', description: 'Send the queued emails')]
class ConstantExpressionSignature extends Command
{
}

?>
