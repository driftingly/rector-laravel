<?php

namespace Rector\Laravel\Tests\Rector\MethodCall\RedirectBackHelperToBackHelperRector\Fixture;

class SkipWithStaticCall
{
    public function run()
    {
        return redirect('/destination')->back()->with('error', 'Incorrect credential.');
    }
}

?>
-----
<?php

namespace Rector\Laravel\Tests\Rector\MethodCall\RedirectBackHelperToBackHelperRector\Fixture;

class SkipWithStaticCall
{
    public function run()
    {
        return redirect('/destination')->back()->with('error', 'Incorrect credential.');
    }
}

?>
