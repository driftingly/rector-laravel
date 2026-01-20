<?php

namespace RectorLaravel\Tests\Rector\MethodCall\EloquentWhereTypeHintClosureParameterRector\Fixture;

use RectorLaravel\Tests\Rector\MethodCall\EloquentWhereTypeHintClosureParameterRector\Source\FooModel;

(new FooModel())->relation()->where(fn ($query) =>
    $query->where('id', 1)
        ->orWhere('id', 2)
);

?>
-----
<?php

namespace RectorLaravel\Tests\Rector\MethodCall\EloquentWhereTypeHintClosureParameterRector\Fixture;

use RectorLaravel\Tests\Rector\MethodCall\EloquentWhereTypeHintClosureParameterRector\Source\FooModel;

(new FooModel())->relation()->where(fn (\Illuminate\Database\Eloquent\Relations\HasMany $query) =>
    $query->where('id', 1)
        ->orWhere('id', 2)
);

?>
