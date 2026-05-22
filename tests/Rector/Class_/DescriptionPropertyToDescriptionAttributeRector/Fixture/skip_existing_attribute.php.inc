<?php

namespace RectorLaravel\Tests\Rector\Class_\DescriptionPropertyToDescriptionAttributeRector\Fixture;

use Illuminate\Console\Command;

#[\Illuminate\Console\Attributes\Description('Send marketing emails to users')]
class SkipExistingAttribute extends Command
{
    protected $description = 'Send marketing emails to users';
}

?>
