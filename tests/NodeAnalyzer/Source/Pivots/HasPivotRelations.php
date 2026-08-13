<?php

namespace RectorLaravel\Tests\NodeAnalyzer\Source\Pivots;

use RectorLaravel\Tests\NodeAnalyzer\Source\Bar;

trait HasPivotRelations
{
    public function inferredPivot()
    {
        return $this->belongsToMany(Bar::class, 'foo_bar');
    }
}
