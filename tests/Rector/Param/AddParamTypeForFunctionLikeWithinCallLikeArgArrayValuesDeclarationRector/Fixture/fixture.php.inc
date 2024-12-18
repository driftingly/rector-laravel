<?php

namespace RectorLaravel\Tests\Rector\Param\AddParamTypeForFunctionLikeWithinCallLikeArgArrayValuesDeclarationRector\Fixture;

\SomeClass::someMethod([
    'key' => function ($value) {
        return $value;
    }
]);

?>
-----
<?php

namespace RectorLaravel\Tests\Rector\Param\AddParamTypeForFunctionLikeWithinCallLikeArgArrayValuesDeclarationRector\Fixture;

\SomeClass::someMethod([
    'key' => function (string $value) {
        return $value;
    }
]);

?>
