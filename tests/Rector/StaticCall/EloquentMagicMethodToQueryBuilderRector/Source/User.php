<?php

declare(strict_types=1);

namespace RectorLaravel\Tests\Rector\StaticCall\EloquentMagicMethodToQueryBuilderRector\Source;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    public static function conflict(): void {}

    protected function scopeConflict(Builder $query): void {}

    protected function scopeFoo(Builder $query): Builder
    {
        return $query;
    }
}
