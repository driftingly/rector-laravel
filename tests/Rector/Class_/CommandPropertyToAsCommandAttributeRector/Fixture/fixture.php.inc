<?php

namespace RectorLaravel\Tests\Rector\Class_\CommandPropertyToAsCommandAttributeRector\Fixture;

use Illuminate\Console\Command;

class SendEmails extends Command
{
    protected $signature = 'mail:send';

    protected $description = 'Send the queued emails';

    public function handle(): void
    {
    }
}

?>
-----
<?php

namespace RectorLaravel\Tests\Rector\Class_\CommandPropertyToAsCommandAttributeRector\Fixture;

use Illuminate\Console\Command;

#[\Symfony\Component\Console\Attribute\AsCommand(name: 'mail:send', description: 'Send the queued emails')]
class SendEmails extends Command
{
    public function handle(): void
    {
    }
}

?>
