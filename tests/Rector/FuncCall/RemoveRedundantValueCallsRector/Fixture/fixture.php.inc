<?php

namespace RectorLaravel\Tests\Rector\FuncCall\RemoveRedundantValueCallsRector\Fixture;

use RectorLaravel\Tests\Rector\FuncCall\RemoveRedundantValueCallsRector\Source\HelperObject;

value(new HelperObject())->store();

$foo = value(new HelperObject());

$user = 'a';

value($user)->store();

$foo = value($user);

?>
-----
<?php

namespace RectorLaravel\Tests\Rector\FuncCall\RemoveRedundantValueCallsRector\Fixture;

use RectorLaravel\Tests\Rector\FuncCall\RemoveRedundantValueCallsRector\Source\HelperObject;

(new HelperObject())->store();

$foo = new HelperObject();

$user = 'a';

$user->store();

$foo = $user;

?>
