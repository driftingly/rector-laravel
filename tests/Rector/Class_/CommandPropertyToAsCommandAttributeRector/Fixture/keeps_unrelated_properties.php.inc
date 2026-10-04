<?php

namespace RectorLaravel\Tests\Rector\Class_\CommandPropertyToAsCommandAttributeRector\Fixture;

use Illuminate\Console\Command;

class KeepsUnrelatedProperties extends Command
{
    protected $signature = 'mail:send';

    protected $description = 'Send the queued emails';

    protected $help = 'Longer help text';

    protected $hidden = true;

    protected $aliases = ['email:send'];
}

?>
-----
<?php

namespace RectorLaravel\Tests\Rector\Class_\CommandPropertyToAsCommandAttributeRector\Fixture;

use Illuminate\Console\Command;

#[\Symfony\Component\Console\Attribute\AsCommand(name: 'mail:send', description: 'Send the queued emails')]
class KeepsUnrelatedProperties extends Command
{
    protected $help = 'Longer help text';

    protected $hidden = true;

    protected $aliases = ['email:send'];
}

?>
