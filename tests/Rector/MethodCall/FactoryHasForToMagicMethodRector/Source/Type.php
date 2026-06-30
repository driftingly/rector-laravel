<?php

namespace RectorLaravel\Tests\Rector\MethodCall\FactoryHasForToMagicMethodRector\Source;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Type extends Model
{
    use HasFactory;

    public function variations(): Builder
    {
        return Variation::query()->where('type_id', $this->id);
    }
}
