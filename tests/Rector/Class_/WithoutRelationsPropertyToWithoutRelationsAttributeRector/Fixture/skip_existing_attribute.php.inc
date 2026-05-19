<?php

namespace RectorLaravel\Tests\Rector\Class_\WithoutRelationsPropertyToWithoutRelationsAttributeRector\Fixture;

use Illuminate\Contracts\Queue\ShouldQueue;

#[\Illuminate\Queue\Attributes\WithoutRelations]
final class SkipExistingAttribute implements ShouldQueue
{
    public $withoutRelations = true;
}

?>
