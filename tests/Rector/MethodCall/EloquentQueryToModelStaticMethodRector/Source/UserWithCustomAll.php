<?php

declare(strict_types=1);

namespace RectorLaravel\Tests\Rector\MethodCall\EloquentQueryToModelStaticMethodRector\Source;

use Illuminate\Database\Eloquent\Model;

class UserWithCustomAll extends Model
{
    public static function all($columns = ['*'])
    {
        return [];
    }
}
