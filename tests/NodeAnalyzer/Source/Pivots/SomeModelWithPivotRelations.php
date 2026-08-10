<?php

namespace RectorLaravel\Tests\NodeAnalyzer\Source\Pivots;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use RectorLaravel\Tests\NodeAnalyzer\Source\Bar;
use RectorLaravel\Tests\NodeAnalyzer\Source\SomeModelWithCustomTableAndPrimaryKey;

class SomeModelWithPivotRelations extends Model
{
    public function inferredPivot()
    {
        return $this->belongsToMany(Bar::class, 'foo_bar');
    }

    public function usingPivot()
    {
        return $this->belongsToMany(Bar::class, 'custom_table')
            ->using(SomeModelWithCustomTableAndPrimaryKey::class);
    }

    public function conflictingUsingPivot()
    {
        return $this->belongsToMany(Bar::class, 'foo_bar')
            ->using(SomeModelWithCustomTableAndPrimaryKey::class);
    }

    /**
     * @return BelongsToMany<Bar, $this, SomeModelWithCustomTableAndPrimaryKey>
     */
    public function genericsPivot(): BelongsToMany
    {
        return $this->belongsToMany(Bar::class, 'custom_table');
    }

    public function unknownTable()
    {
        return $this->belongsToMany(Bar::class, 'no_such_table');
    }
}
