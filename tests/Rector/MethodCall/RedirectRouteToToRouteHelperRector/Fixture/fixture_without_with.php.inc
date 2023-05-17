<?php

namespace RectorLaravel\Tests\Rector\MethodCall\RedirectRouteToToRouteHelperRector\Fixture;

use Illuminate\Support\Facades\Redirect;

class FixtureWithoutWith
{
    public function store()
    {
        return redirect()->route('home');
    }

    public function update()
    {
        return Redirect::route('home');
    }
}

?>
-----
<?php

namespace RectorLaravel\Tests\Rector\MethodCall\RedirectRouteToToRouteHelperRector\Fixture;

use Illuminate\Support\Facades\Redirect;

class FixtureWithoutWith
{
    public function store()
    {
        return to_route('home');
    }

    public function update()
    {
        return to_route('home');
    }
}

?>
