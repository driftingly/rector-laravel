<?php

namespace RectorLaravel\Tests\Rector\ClassMethod\AddArgumentDefaultValueRector\Fixture;

use Illuminate\Contracts\Events\Dispatcher;

class Fixture extends Dispatcher
{
    public function listen($events, $listener)
    {

    }
}

?>
-----
<?php

namespace RectorLaravel\Tests\Rector\ClassMethod\AddArgumentDefaultValueRector\Fixture;

use Illuminate\Contracts\Events\Dispatcher;

class Fixture extends Dispatcher
{
    public function listen($events, $listener = null)
    {

    }
}

?>
