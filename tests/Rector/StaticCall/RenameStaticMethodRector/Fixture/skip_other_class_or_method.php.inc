<?php

namespace RectorLaravel\Tests\Rector\StaticCall\RenameStaticMethodRector\Fixture;

use RectorLaravel\Tests\Rector\StaticCall\RenameStaticMethodRector\Source\NewClass;
use RectorLaravel\Tests\Rector\StaticCall\RenameStaticMethodRector\Source\OldClass;

class SkipOtherClassOrMethod
{
    public function run()
    {
        NewClass::get('name');
        OldClass::other();
    }
}
