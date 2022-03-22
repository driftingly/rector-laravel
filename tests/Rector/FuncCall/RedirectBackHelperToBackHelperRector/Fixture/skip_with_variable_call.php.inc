<?php

namespace Rector\Laravel\Tests\Rector\FuncCall\HelperFuncCallToFacadeClassRector\Fixture;

use Illuminate\Support\Facades\Redirect;

class SkipWithStaticCall
{
    public function run(Redirect $redirect)
    {
        return $redirect->back()->with('error', 'Incorrect credential.');
    }
}

?>
-----
<?php

namespace Rector\Laravel\Tests\Rector\FuncCall\HelperFuncCallToFacadeClassRector\Fixture;

use Illuminate\Support\Facades\Redirect;

class SkipWithStaticCall
{
    public function run(Redirect $redirect)
    {
        return $redirect->back()->with('error', 'Incorrect credential.');
    }
}

?>
