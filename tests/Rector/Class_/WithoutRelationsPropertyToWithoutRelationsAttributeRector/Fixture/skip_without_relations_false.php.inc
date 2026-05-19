<?php

namespace RectorLaravel\Tests\Rector\Class_\WithoutRelationsPropertyToWithoutRelationsAttributeRector\Fixture;

use Illuminate\Contracts\Queue\ShouldQueue;

final class SkipWithoutRelationsFalse implements ShouldQueue
{
    public $withoutRelations = false;
}

?>
