<?php

namespace RectorLaravel\Tests\Rector\MethodCall\EloquentWhereRelationTypeHintingParameterRector\Fixture;

use RectorLaravel\Tests\Rector\MethodCall\EloquentWhereRelationTypeHintingParameterRector\Source\FooModel;

(new FooModel)->relation()->whereHas('posts', function ($query) {
    $query->where('is_published', true);
});

?>
-----
<?php

namespace RectorLaravel\Tests\Rector\MethodCall\EloquentWhereRelationTypeHintingParameterRector\Fixture;

use RectorLaravel\Tests\Rector\MethodCall\EloquentWhereRelationTypeHintingParameterRector\Source\FooModel;

(new FooModel)->relation()->whereHas('posts', function (\Illuminate\Database\Eloquent\Relations\HasMany $query) {
    $query->where('is_published', true);
});

?>
