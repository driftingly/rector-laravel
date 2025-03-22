<?php

namespace RectorLaravel\Tests\Rector\FuncCall\HelperFuncCallToFacadeClassRector\Fixture;

class Fixture
{
    public function run()
    {
        resolve('translator')->trans('value');

        return app('translator')->trans('value');
    }
}

?>
-----
<?php

namespace RectorLaravel\Tests\Rector\FuncCall\HelperFuncCallToFacadeClassRector\Fixture;

class Fixture
{
    public function run()
    {
        \Illuminate\Support\Facades\App::get('translator')->trans('value');

        return \Illuminate\Support\Facades\App::get('translator')->trans('value');
    }
}

?>
