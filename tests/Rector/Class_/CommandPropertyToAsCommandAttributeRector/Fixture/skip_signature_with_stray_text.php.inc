<?php

namespace RectorLaravel\Tests\Rector\Class_\CommandPropertyToAsCommandAttributeRector\Fixture;

use Illuminate\Console\Command;

class SkipSignatureWithStrayText extends Command
{
    protected $signature = 'mail:send {user} unparsed';
}

?>
