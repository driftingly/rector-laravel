<?php

namespace RectorLaravel\Tests\Rector\Class_\AliasesPropertyToAliasesAttributeRector\Fixture;

use Illuminate\Console\Command;

class SendEmails extends Command
{
    protected $aliases = ['email:send'];
}

?>
-----
<?php

namespace RectorLaravel\Tests\Rector\Class_\AliasesPropertyToAliasesAttributeRector\Fixture;

use Illuminate\Console\Command;

#[\Illuminate\Console\Attributes\Aliases(['email:send'])]
class SendEmails extends Command
{
}

?>
