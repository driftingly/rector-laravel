<?php

namespace RectorLaravel\Tests\Rector\Class_\CommandPropertyToAsCommandAttributeRector\Fixture;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class SkipOwnParameterMethodNotReturningArray extends Command
{
    protected $signature = 'mail:send {--queue}';

    protected function getOptions(): array
    {
        return array_merge(parent::getOptions(), [['delay', 'd', InputOption::VALUE_REQUIRED]]);
    }
}

?>
