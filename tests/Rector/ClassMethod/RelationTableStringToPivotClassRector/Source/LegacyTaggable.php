<?php

namespace RectorLaravel\Tests\Rector\ClassMethod\RelationTableStringToPivotClassRector\Source;

use Illuminate\Database\Eloquent\Relations\MorphPivot;

class LegacyTaggable extends MorphPivot
{
    protected $table = 'legacy_taggables';
}
