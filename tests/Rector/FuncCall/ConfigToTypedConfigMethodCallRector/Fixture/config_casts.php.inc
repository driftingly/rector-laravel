<?php

namespace RectorLaravel\Tests\Rector\FuncCall\ConfigToTypedConfigMethodCallRector\Fixture;

class ConfigCasts
{
    public function run()
    {
        $name = (string) config('app.name');
        $lifetime = (int) config('session.lifetime');
        $debug = (bool) config('app.debug');
        $version = (float) config('app.version');
        $connections = (array) config('database.connections');
    }
}

?>
-----
<?php

namespace RectorLaravel\Tests\Rector\FuncCall\ConfigToTypedConfigMethodCallRector\Fixture;

class ConfigCasts
{
    public function run()
    {
        $name = config()->string('app.name');
        $lifetime = config()->integer('session.lifetime');
        $debug = config()->boolean('app.debug');
        $version = config()->float('app.version');
        $connections = config()->array('database.connections');
    }
}

?>
