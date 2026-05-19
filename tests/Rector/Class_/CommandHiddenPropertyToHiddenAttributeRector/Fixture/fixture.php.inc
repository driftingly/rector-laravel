<?php

namespace RectorLaravel\Tests\Rector\Class_\CommandHiddenPropertyToHiddenAttributeRector\Fixture;

use Illuminate\Console\Command;

class SendEmails extends Command
{
    protected $hidden = true;
}

?>
-----
<?php

namespace RectorLaravel\Tests\Rector\Class_\CommandHiddenPropertyToHiddenAttributeRector\Fixture;

use Illuminate\Console\Command;

#[\Illuminate\Console\Attributes\Hidden]
class SendEmails extends Command
{
}

?>
