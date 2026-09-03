<?php

namespace RectorLaravel\Tests\Rector\Class_\CommandPropertyToAsCommandAttributeRector\Fixture;

class SkipNotCommand
{
    protected $signature = 'mail:send';

    protected $description = 'Send the queued emails';
}

?>
