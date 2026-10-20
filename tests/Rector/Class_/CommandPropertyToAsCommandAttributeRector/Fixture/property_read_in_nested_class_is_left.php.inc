<?php

namespace RectorLaravel\Tests\Rector\Class_\CommandPropertyToAsCommandAttributeRector\Fixture;

use Illuminate\Console\Command;

class PropertyReadInNestedClassIsLeft extends Command
{
    protected $signature = 'mail:send';

    protected $description = 'Send the queued emails';

    public function handle(): void
    {
        $mailer = new class {
            public $description = 'mailer';

            public function describe(): string
            {
                return $this->description;
            }
        };
    }
}

?>
-----
<?php

namespace RectorLaravel\Tests\Rector\Class_\CommandPropertyToAsCommandAttributeRector\Fixture;

use Illuminate\Console\Command;

#[\Symfony\Component\Console\Attribute\AsCommand(name: 'mail:send', description: 'Send the queued emails')]
class PropertyReadInNestedClassIsLeft extends Command
{
    public function handle(): void
    {
        $mailer = new class {
            public $description = 'mailer';

            public function describe(): string
            {
                return $this->description;
            }
        };
    }
}

?>
