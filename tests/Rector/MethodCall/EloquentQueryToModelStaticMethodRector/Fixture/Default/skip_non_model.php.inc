<?php

namespace RectorLaravel\Tests\Rector\MethodCall\EloquentQueryToModelStaticMethodRector\Fixture\Default;

use RectorLaravel\Tests\Rector\MethodCall\EloquentQueryToModelStaticMethodRector\Source\NotAModel;

NotAModel::query()->get();
$unknown::query()->get();
UnknownClass::query()->get();

?>
