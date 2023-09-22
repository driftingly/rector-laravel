<?php

namespace RectorLaravel\Tests\Rector\MethodCall\EloquentWhereRelationTypeHintingParameterRector\Fixture;

$obj->whereHas('posts', fn ($query) =>
    $query->where('is_published', true)
);

RandomClass::whereHas('posts', fn ($query) =>
    $query->where('is_published', true)
);

?>
