<?php

namespace RectorLaravel\Tests\Rector\Class_\SignaturePropertyToSignatureAttributeRector\Fixture;

use Illuminate\Console\Command;

#[\Illuminate\Console\Attributes\Signature('mail:send {user}')]
class SkipExistingAttribute extends Command
{
    protected $signature = 'mail:send {user}';
}

?>
