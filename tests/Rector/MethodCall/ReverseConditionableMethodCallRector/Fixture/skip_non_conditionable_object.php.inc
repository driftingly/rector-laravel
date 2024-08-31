<?php

namespace RectorLaravel\Tests\Rector\MethodCall\ReverseConditionableMethodCallRector\Fixture;

$conditionable = new \stdClass();

$conditionable->when(!true, function () {});
$conditionable->unless(!false, function () {});

?>
