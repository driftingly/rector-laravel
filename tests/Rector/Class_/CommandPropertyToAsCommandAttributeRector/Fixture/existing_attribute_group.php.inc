<?php

namespace RectorLaravel\Tests\Rector\Class_\CommandPropertyToAsCommandAttributeRector\Fixture;

use Illuminate\Console\Command;

#[\Attribute]
class ExistingAttributeGroup extends Command
{
    protected $signature = 'mail:send';
}

?>
-----
<?php

namespace RectorLaravel\Tests\Rector\Class_\CommandPropertyToAsCommandAttributeRector\Fixture;

use Illuminate\Console\Command;

#[\Attribute]
#[\Symfony\Component\Console\Attribute\AsCommand(name: 'mail:send')]
class ExistingAttributeGroup extends Command
{
}

?>
