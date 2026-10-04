<?php

namespace RectorLaravel\Tests\Rector\Class_\CommandPropertyToAsCommandAttributeRector\Fixture;

use Illuminate\Console\Command;

class SkipNullDescriptionReadInClass extends Command
{
    protected $signature = 'mail:send';

    protected $description = null;

    public function handle(): void
    {
        var_dump($this->description === null);
    }
}

?>
