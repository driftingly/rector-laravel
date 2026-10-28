<?php

namespace RectorLaravel\Tests\Rector\Class_\CommandPropertyToAsCommandAttributeRector\Fixture;

use Illuminate\Console\Command;

class SkipSignatureReadInClass extends Command
{
    protected $signature = 'mail:send';

    public function handle(): void
    {
        echo $this->signature;
    }
}

?>
