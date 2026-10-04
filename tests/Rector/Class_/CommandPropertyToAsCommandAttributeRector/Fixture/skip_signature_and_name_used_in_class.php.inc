<?php

namespace RectorLaravel\Tests\Rector\Class_\CommandPropertyToAsCommandAttributeRector\Fixture;

use Illuminate\Console\Command;

class SkipSignatureAndNameUsedInClass extends Command
{
    protected $signature = 'mail:send';

    protected $name = 'mail:dispatch';

    public function handle(): void
    {
        echo $this->name;
    }
}

?>
