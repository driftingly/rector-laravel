<?php

namespace RectorLaravel\Tests\Rector\Class_\CommandPropertyToAsCommandAttributeRector\Fixture;

use Illuminate\Console\Command;

class SkipPropertyUsedInClass extends Command
{
    protected $signature = 'mail:send';

    protected $description = 'Send the queued emails';

    public function handle(): void
    {
        echo $this->description;
    }
}

?>
