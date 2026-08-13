<?php

namespace RectorLaravel\Tests\NodeAnalyzer\Source\Pivots;

use Illuminate\Database\Eloquent\Relations\MorphPivot;

class Taggable extends MorphPivot
{
    protected $table = 'taggables';

    public function tags()
    {
        return $this->morphToMany(Tag::class, 'taggable', 'taggables');
    }
}
