<?php

namespace RectorLaravel\Tests\Rector\Class_\CommandPropertyToAsCommandAttributeRector\Fixture;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class SkipSignatureOptionalArgumentBeforeOwnArguments extends Command
{
    protected $signature = 'mail:send {user?}';

    protected function getArguments(): array
    {
        return [['cc', InputArgument::REQUIRED]];
    }
}

?>
