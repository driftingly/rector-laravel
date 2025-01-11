<?php

namespace RectorLaravel\Tests\Rector\MethodCall\EloquentWhereTypeHintClosureParameterRector\Fixture;

use RectorLaravel\Tests\Rector\MethodCall\EloquentWhereTypeHintClosureParameterRector\Source\FooModel;

FooModel::creating('foo', function ($model) {
    dump($model);
});

?>
