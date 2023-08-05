<?php

namespace RectorLaravel\Tests\Rector\Expr\SubStrToStartsWithOrEndsWithStaticMethodCallRector\Fixture;

if (substr($value, -1, 1) === 'a') {
    return true;
}

?>
-----
<?php

namespace RectorLaravel\Tests\Rector\Expr\SubStrToStartsWithOrEndsWithStaticMethodCallRector\Fixture;

if (substr($value, -1, 1) === 'a') {
    return true;
}

?>
