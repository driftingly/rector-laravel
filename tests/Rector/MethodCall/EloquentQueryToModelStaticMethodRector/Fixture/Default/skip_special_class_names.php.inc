<?php

namespace RectorLaravel\Tests\Rector\MethodCall\EloquentQueryToModelStaticMethodRector\Fixture\Default;

use RectorLaravel\Tests\Rector\MethodCall\EloquentQueryToModelStaticMethodRector\Source\User;

class Admin extends User
{
    public static function everything()
    {
        return [parent::query()->get(), self::query()->get(), static::query()->get()];
    }
}

?>
