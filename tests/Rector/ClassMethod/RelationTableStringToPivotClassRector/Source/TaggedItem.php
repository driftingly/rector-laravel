<?php

namespace RectorLaravel\Tests\Rector\ClassMethod\RelationTableStringToPivotClassRector\Source;

use Illuminate\Database\Eloquent\Relations\MorphPivot;

class TaggedItem extends MorphPivot
{
    protected $table = 'legacy_tagged_items';
}
