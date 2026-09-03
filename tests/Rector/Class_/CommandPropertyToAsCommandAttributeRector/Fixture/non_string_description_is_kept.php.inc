<?php

namespace RectorLaravel\Tests\Rector\Class_\CommandPropertyToAsCommandAttributeRector\Fixture;

use Illuminate\Console\Command;

class NonStringDescriptionIsKept extends Command
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

#[\Symfony\Component\Console\Attribute\AsCommand(name: 'mail:send')]
class NonStringDescriptionIsKept extends Command
{
    protected $description = self::DEFAULT_DESCRIPTION;

    private const DEFAULT_DESCRIPTION = 'Send the queued emails';
}

?>
