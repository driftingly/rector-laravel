<?php

namespace RectorLaravel\Tests\Rector\StaticCall\RenameStaticMethodRector\Fixture;

use RectorLaravel\Tests\Rector\StaticCall\RenameStaticMethodRector\Source\OldClass;

class RenameStaticMethod
{
    public function run()
    {
        OldClass::get('name');
        OldClass::OldMethod();
    }
}

?>
-----
<?php

namespace RectorLaravel\Tests\Rector\StaticCall\RenameStaticMethodRector\Fixture;

use RectorLaravel\Tests\Rector\StaticCall\RenameStaticMethodRector\Source\OldClass;

class RenameStaticMethod
{
    public function run()
    {
        \RectorLaravel\Tests\Rector\StaticCall\RenameStaticMethodRector\Source\NewClass::input('name');
        OldClass::newMethod();
    }
}

?>
