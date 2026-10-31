<?php

namespace RectorLaravel\Tests\Rector\Class_\CommandPropertyToAsCommandAttributeRector\Fixture;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;

class SkipOwnParameterMethodWithStatementBeforeReturn extends Command
{
    protected $signature = 'mail:send {--queue}';

    protected function getOptions(): array
    {
        $mode = InputOption::VALUE_REQUIRED;

        return [['delay', 'd', $mode]];
    }
}

?>
