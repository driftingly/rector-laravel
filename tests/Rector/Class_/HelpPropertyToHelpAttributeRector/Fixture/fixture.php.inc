<?php

namespace RectorLaravel\Tests\Rector\Class_\HelpPropertyToHelpAttributeRector\Fixture;

use Illuminate\Console\Command;

class SendEmails extends Command
{
    protected $help = 'This command sends emails to all users';
}

?>
-----
<?php

namespace RectorLaravel\Tests\Rector\Class_\HelpPropertyToHelpAttributeRector\Fixture;

use Illuminate\Console\Command;

#[\Illuminate\Console\Attributes\Help('This command sends emails to all users')]
class SendEmails extends Command
{
}

?>
