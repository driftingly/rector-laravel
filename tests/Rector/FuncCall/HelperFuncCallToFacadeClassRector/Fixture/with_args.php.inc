<?php

namespace RectorLaravel\Tests\Rector\FuncCall\HelperFuncCallToFacadeClassRector\Fixture;

class WithArgs
{
    public function run()
    {
        resolve(SomeClass::class, ['argument' => 'value']);

        return app(OtherClass::class, ['argument' => 'value']);
    }
}

?>
-----
<?php

namespace RectorLaravel\Tests\Rector\FuncCall\HelperFuncCallToFacadeClassRector\Fixture;

class WithArgs
{
    public function run()
    {
        \Illuminate\Support\Facades\App::make(SomeClass::class, ['argument' => 'value']);

        return \Illuminate\Support\Facades\App::make(OtherClass::class, ['argument' => 'value']);
    }
}

?>
