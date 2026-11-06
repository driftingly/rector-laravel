<?php

declare(strict_types=1);

namespace RectorLaravel\Tests\Rector\MethodCall\EloquentQueryGetToAllRector\Source;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class UserWithCustomQuery extends Model
{
    public static function query(): Builder
    {
        return parent::query()->where('active', true);
    }
}
