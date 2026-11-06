<?php

namespace RectorLaravel\Tests\Rector\MethodCall\EloquentQueryGetToAllRector\Fixture;

use RectorLaravel\Tests\Rector\MethodCall\EloquentQueryGetToAllRector\Source\User;

class Admin extends User
{
    public static function everything()
    {
        return [parent::query()->get(), self::query()->get()];
    }
}

?>
