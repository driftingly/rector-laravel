<?php

declare(strict_types=1);

namespace RectorLaravel\Tests\Rector\MethodCall\EloquentQueryToModelStaticMethodRector\Source;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

abstract class BaseModel extends Model
{
    public static function active(): Builder
    {
        return static::query()->where('active', true);
    }

    public static function ofStatus(string $status): Builder
    {
        return static::query()->where('status', $status);
    }
}
