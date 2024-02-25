<?php

namespace RectorLaravel\Tests\Rector\FuncCall\RemoveRedundantWithCallsRector\Fixture;

use RectorLaravel\Tests\Rector\FuncCall\RemoveRedundantWithCallsRector\Source\HelperObject;

with(new HelperObject())->store();

$foo = with(new HelperObject());

$user = 'a';

with($user)->store();

$foo = with($user);

?>
-----
<?php

namespace RectorLaravel\Tests\Rector\FuncCall\RemoveRedundantWithCallsRector\Fixture;

use RectorLaravel\Tests\Rector\FuncCall\RemoveRedundantWithCallsRector\Source\HelperObject;

(new HelperObject())->store();

$foo = new HelperObject();

$user = 'a';

$user->store();

$foo = $user;

?>
