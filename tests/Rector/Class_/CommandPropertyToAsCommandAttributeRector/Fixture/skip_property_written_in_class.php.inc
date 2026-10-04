<?php

namespace RectorLaravel\Tests\Rector\Class_\CommandPropertyToAsCommandAttributeRector\Fixture;

use Illuminate\Console\Command;

class SkipPropertyWrittenInClass extends Command
{
    protected $signature = 'mail:send';

    protected $description = 'Send the queued emails';

    public function __construct()
    {
        $this->description .= ' now';

        parent::__construct();
    }
}

?>
