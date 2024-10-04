<?php

namespace RectorLaravel\Tests\Sets\TypeDeclaration\Eloquent\Fixture;

\Illuminate\Database\Eloquent\Model::handleLazyLoadingViolationUsing(function ($model, $relation) {
    $class = $model::class;

    info("Attempted to lazy load [{$relation}] on model [{$class}].");
});

?>
-----
<?php

namespace RectorLaravel\Tests\Sets\TypeDeclaration\Eloquent\Fixture;

\Illuminate\Database\Eloquent\Model::handleLazyLoadingViolationUsing(function (\Illuminate\Database\Eloquent\Model $model, string $relation) {
    $class = $model::class;

    info("Attempted to lazy load [{$relation}] on model [{$class}].");
});

?>
