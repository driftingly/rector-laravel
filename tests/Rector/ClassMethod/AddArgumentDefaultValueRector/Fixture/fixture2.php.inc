<?php

namespace Rector\Laravel\Tests\Rector\ClassMethod\AddArgumentDefaultValueRector\Fixture;

use Illuminate\Contracts\Events\Dispatcher;

class Fixture2 extends Dispatcher
{
    public function listen($events, $listener = null)
    {

    }
}

?>
-----
<?php

namespace Rector\Laravel\Tests\Rector\ClassMethod\AddArgumentDefaultValueRector\Fixture;

use Illuminate\Contracts\Events\Dispatcher;

class Fixture2 extends Dispatcher
{
    public function listen($events, $listener = null)
    {

    }
}

?>
