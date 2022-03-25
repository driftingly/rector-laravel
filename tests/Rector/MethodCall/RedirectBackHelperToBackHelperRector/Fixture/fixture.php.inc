<?php

namespace Rector\Laravel\Tests\Rector\MethodCall\RedirectBackHelperToBackHelperRector\Fixture;

use Illuminate\Support\Facades\Redirect;

class Fixture
{
    public function store()
    {
        return redirect()->back()->with('error', 'Incorrect credential.');
    }

    public function update()
    {
        return Redirect::back()->with('error', 'Incorrect credential.');
    }
}

?>
-----
<?php

namespace Rector\Laravel\Tests\Rector\MethodCall\RedirectBackHelperToBackHelperRector\Fixture;

use Illuminate\Support\Facades\Redirect;

class Fixture
{
    public function store()
    {
        return back()->with('error', 'Incorrect credential.');
    }

    public function update()
    {
        return back()->with('error', 'Incorrect credential.');
    }
}

?>
