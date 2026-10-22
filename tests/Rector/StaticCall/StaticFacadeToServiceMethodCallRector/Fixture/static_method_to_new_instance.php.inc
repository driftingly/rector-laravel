<?php

namespace RectorLaravel\Tests\Rector\StaticCall\StaticFacadeToServiceMethodCallRector\Fixture;

class StaticMethodToNewInstance
{
    public static function run()
    {
        return \Illuminate\Support\Facades\Auth::user();
    }
}

?>
-----
<?php

namespace RectorLaravel\Tests\Rector\StaticCall\StaticFacadeToServiceMethodCallRector\Fixture;

class StaticMethodToNewInstance
{
    public static function run()
    {
        return (new \Illuminate\Auth\AuthManager())->user();
    }
}

?>
