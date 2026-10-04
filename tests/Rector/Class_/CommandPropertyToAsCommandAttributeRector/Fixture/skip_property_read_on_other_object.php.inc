<?php

namespace RectorLaravel\Tests\Rector\Class_\CommandPropertyToAsCommandAttributeRector\Fixture;

use Illuminate\Console\Command;

class SkipPropertyReadOnOtherObject extends Command
{
    protected $signature = 'mail:send';

    protected $description = 'Send the queued emails';

    public function isSameAs(self $command): bool
    {
        return $command->description === 'Send the queued emails';
    }
}

?>
