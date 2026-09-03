<?php

namespace RectorLaravel\Tests\Rector\StaticCall\EventStringToClassConstantRector\Fixture;

event('auth.login');

?>
-----
<?php

namespace RectorLaravel\Tests\Rector\StaticCall\EventStringToClassConstantRector\Fixture;

event(\Illuminate\Auth\Events\Login::class);

?>
