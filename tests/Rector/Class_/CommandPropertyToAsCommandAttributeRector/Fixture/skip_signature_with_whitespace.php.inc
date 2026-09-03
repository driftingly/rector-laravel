<?php

namespace RectorLaravel\Tests\Rector\Class_\CommandPropertyToAsCommandAttributeRector\Fixture;

use Illuminate\Console\Command;

class SkipSignatureWithWhitespace extends Command
{
    protected $signature = '
        mail:send
    ';
}

?>
