<?php

namespace RectorLaravel\Tests\Rector\Class_\AliasesPropertyToAliasesAttributeRector\Fixture;

use Illuminate\Console\Command;

#[\Illuminate\Console\Attributes\Aliases(['email:send'])]
class SkipExistingAttribute extends Command
{
    protected $aliases = ['email:send'];
}

?>
