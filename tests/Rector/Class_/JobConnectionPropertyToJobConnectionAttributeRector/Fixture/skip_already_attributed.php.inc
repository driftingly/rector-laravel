<?php

namespace RectorLaravel\Tests\Rector\Class_\JobConnectionPropertyToJobConnectionAttributeRector\Fixture;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\Attributes\Connection;

#[Connection('redis')]
final class SkipAlreadyAttributedJobConnectionJob implements ShouldQueue
{
    public $connection = 'redis';
}

?>
