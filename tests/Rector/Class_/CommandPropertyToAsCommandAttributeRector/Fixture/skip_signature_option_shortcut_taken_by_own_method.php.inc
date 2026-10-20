<?php

namespace RectorLaravel\Tests\Rector\Class_\CommandPropertyToAsCommandAttributeRector\Fixture;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class SkipSignatureOptionShortcutTakenByOwnMethod extends Command
{
    protected $signature = 'mail:send {--Q|queue}';

    protected function getOptions(): array
    {
        return [new InputOption('quiet-queue', 'Q', InputOption::VALUE_NONE)];
    }
}

?>
