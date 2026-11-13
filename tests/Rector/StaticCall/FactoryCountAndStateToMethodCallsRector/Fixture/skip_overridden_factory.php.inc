<?php

namespace RectorLaravel\Tests\Rector\StaticCall\FactoryCountAndStateToMethodCallsRector\Fixture;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomFactoryModel extends Model
{
    use HasFactory;

    public static function factory($variant = 1)
    {
    }
}

class CustomFactoryChild extends CustomFactoryModel {}

CustomFactoryModel::factory(3);
CustomFactoryChild::factory(3);
