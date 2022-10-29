<?php

namespace RectorLaravel\Tests\Rector\ClassMethod\AddParentRegisterToEventServiceProviderRector\Fixture;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class OnTheLastLine extends ServiceProvider
{
    public function register()
    {
        $value = 1000;
        parent::register();
    }
}

?>
-----
<?php

namespace RectorLaravel\Tests\Rector\ClassMethod\AddParentRegisterToEventServiceProviderRector\Fixture;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class OnTheLastLine extends ServiceProvider
{
    public function register()
    {
        parent::register();
        $value = 1000;
    }
}

?>
