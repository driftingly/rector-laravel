<?php

namespace RectorLaravel\Tests\Rector\MethodCall\EloquentQueryGetToAllRector\Fixture;

use RectorLaravel\Tests\Rector\MethodCall\EloquentQueryGetToAllRector\Source\NotAModel;

NotAModel::query()->get();
$unknown::query()->get();
UnknownClass::query()->get();

?>
