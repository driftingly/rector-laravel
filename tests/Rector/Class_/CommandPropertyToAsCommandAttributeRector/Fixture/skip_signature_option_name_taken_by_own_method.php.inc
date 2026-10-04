<?php

namespace RectorLaravel\Tests\Rector\Class_\CommandPropertyToAsCommandAttributeRector\Fixture;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class SkipSignatureOptionNameTakenByOwnMethod extends Command
{
    protected $signature = 'mail:send {--queue}';

    protected function getOptions(): array
    {
        return [['queue', null, InputOption::VALUE_REQUIRED]];
    }
}

?>
