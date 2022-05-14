<?php

namespace Rector\Laravel\Tests\Rector\MethodCall\RedirectRouteToToRouteHelperRector\Fixture;

use Illuminate\Support\Facades\Redirect;

class Fixture
{
    public function store()
    {
        return redirect()->route('home')->with('error', 'Incorrect credential.');
    }

    public function update()
    {
        return Redirect::route('home')->with('error', 'Incorrect credential.');
    }
}

?>
-----
<?php

namespace Rector\Laravel\Tests\Rector\MethodCall\RedirectBackToBackHelperRector\Fixture;

use Illuminate\Support\Facades\Redirect;

class Fixture
{
    public function store()
    {
        return to_route('home')->with('error', 'Incorrect credential.');
    }

    public function update()
    {
        return to_route('home')->with('error', 'Incorrect credential.');
    }
}

?>
