<?php

namespace RectorLaravel\Tests\Rector\New_\AddGuardToLoginEventRector\Fixture;

use Illuminate\Auth\Events\Login;

final class SomeClass
{
    public function run(): void
    {
        $loginEvent = new Login('user', false);
    }
}

?>
-----
<?php

namespace RectorLaravel\Tests\Rector\New_\AddGuardToLoginEventRector\Fixture;

use Illuminate\Auth\Events\Login;

final class SomeClass
{
    public function run(): void
    {
        $guard = config('auth.defaults.guard');
        $loginEvent = new Login($guard, 'user', false);
    }
}

?>
