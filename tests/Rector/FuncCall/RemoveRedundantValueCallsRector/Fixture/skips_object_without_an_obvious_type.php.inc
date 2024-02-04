<?php

namespace RectorLaravel\Tests\Rector\FuncCall\RemoveRedundantValueCallsRector\Fixture;

value(new UndefinedClass())->store();

$foo = value(new UndefinedClass());

$user = new UndefinedClass();

value($user)->store();
