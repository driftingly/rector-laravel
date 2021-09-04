<?php

namespace Rector\Laravel\Tests\Rector\ClassMethod\AddParentRegisterToEventServiceProviderRector\Fixture;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    public function register()
    {
    }
}

?>
-----
<?php

namespace Rector\Laravel\Tests\Rector\ClassMethod\AddParentRegisterToEventServiceProviderRector\Fixture;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    public function register()
    {
        parent::register();
    }
}

?>
