<?php

namespace RectorLaravel\Tests\Rector\FuncCall\RemoveRedundantWithCallsRector\Fixture;

with(new UndefinedClass(), new AnotherUndefinedType())->store();

$foo = with(new UndefinedClass(), new AnotherUndefinedType());

$user = new UndefinedClass();
$bar = new AnotherUndefinedType();

with($user, $bar)->store();
