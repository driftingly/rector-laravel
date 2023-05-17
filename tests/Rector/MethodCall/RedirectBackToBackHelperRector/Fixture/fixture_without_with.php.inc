<?php

namespace RectorLaravel\Tests\Rector\MethodCall\RedirectBackToBackHelperRector\Fixture;

use Illuminate\Support\Facades\Redirect;

class FixtureWithoutWith
{
    public function store()
    {
        return redirect()->back();
    }

    public function update()
    {
        return Redirect::back();
    }
}

?>
-----
<?php

namespace RectorLaravel\Tests\Rector\MethodCall\RedirectBackToBackHelperRector\Fixture;

use Illuminate\Support\Facades\Redirect;

class FixtureWithoutWith
{
    public function store()
    {
        return back();
    }

    public function update()
    {
        return back();
    }
}

?>
