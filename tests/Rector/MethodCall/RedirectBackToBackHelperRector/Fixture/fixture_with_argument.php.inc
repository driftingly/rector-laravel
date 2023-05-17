<?php

namespace RectorLaravel\Tests\Rector\MethodCall\RedirectBackToBackHelperRector\Fixture;

use Illuminate\Support\Facades\Redirect;

class Fixture
{
    public function store()
    {
        return redirect()->back(302);
    }

    public function update()
    {
        return Redirect::back(302);
    }
}

?>
-----
<?php

namespace RectorLaravel\Tests\Rector\MethodCall\RedirectBackToBackHelperRector\Fixture;

use Illuminate\Support\Facades\Redirect;

class Fixture
{
    public function store()
    {
        return back(302);
    }

    public function update()
    {
        return back(302);
    }
}

?>
