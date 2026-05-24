<?php

namespace RectorLaravel\Tests\Rector\Class_\SignaturePropertyToSignatureAttributeRector\Fixture;

use Illuminate\Console\Command;

class SendEmails extends Command
{
    protected $signature = 'mail:send {user}';
}

?>
-----
<?php

namespace RectorLaravel\Tests\Rector\Class_\SignaturePropertyToSignatureAttributeRector\Fixture;

use Illuminate\Console\Command;

#[\Illuminate\Console\Attributes\Signature('mail:send {user}')]
class SendEmails extends Command
{
}

?>
