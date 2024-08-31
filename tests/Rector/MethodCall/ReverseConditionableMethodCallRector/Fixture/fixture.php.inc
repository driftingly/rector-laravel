<?php

namespace RectorLaravel\Tests\Rector\MethodCall\ReverseConditionableMethodCallRector\Fixture;

use RectorLaravel\Tests\Rector\MethodCall\ReverseConditionableMethodCallRector\Source\ConditionableExample;

$conditionable = new ConditionableExample();

$conditionable->when(!true, function () {});
$conditionable->unless(!false, function () {});

?>
-----
<?php

namespace RectorLaravel\Tests\Rector\MethodCall\ReverseConditionableMethodCallRector\Fixture;

use RectorLaravel\Tests\Rector\MethodCall\ReverseConditionableMethodCallRector\Source\ConditionableExample;

$conditionable = new ConditionableExample();

$conditionable->unless(true, function () {});
$conditionable->when(false, function () {});

?>
