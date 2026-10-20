<?php

namespace RectorLaravel\Tests\Rector\Class_\CommandPropertyToAsCommandAttributeRector\Fixture;

use Illuminate\Console\Command;

class ConstantExpressionDescription extends Command
{
    protected $signature = 'mail:send';

    protected $description = self::DEFAULT_DESCRIPTION;

    private const DEFAULT_DESCRIPTION = 'Send the queued emails';
}

?>
-----
<?php

namespace RectorLaravel\Tests\Rector\Class_\CommandPropertyToAsCommandAttributeRector\Fixture;

use Illuminate\Console\Command;

#[\Symfony\Component\Console\Attribute\AsCommand(name: 'mail:send', description: self::DEFAULT_DESCRIPTION)]
class ConstantExpressionDescription extends Command
{
    private const DEFAULT_DESCRIPTION = 'Send the queued emails';
}

?>
