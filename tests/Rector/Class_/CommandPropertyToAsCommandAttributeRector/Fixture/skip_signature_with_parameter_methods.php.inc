<?php

namespace RectorLaravel\Tests\Rector\Class_\CommandPropertyToAsCommandAttributeRector\Fixture;

use Illuminate\Console\Command;

class SkipSignatureWithParameterMethods extends Command
{
    protected $signature = 'mail:send';

    protected function getOptions(): array
    {
        return [['queue', null, null, 'Queue the mail']];
    }
}

?>
