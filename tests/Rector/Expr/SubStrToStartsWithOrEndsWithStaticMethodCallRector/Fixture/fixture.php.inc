<?php

namespace RectorLaravel\Tests\Rector\Expr\SubStrToStartsWithOrEndsWithStaticMethodCallRector\Fixture;

if (substr($value, 0, 1) === 'a') {
    return true;
}

if (substr($value, -1) === 'a') {
    return true;
}

if ('a' === substr($value, 0, 1)) {
    return true;
}

if ($prefix === substr($value, 0, 1)) {
    return true;
}

if (substr($value, 0, 1) == 'a') {
    return true;
}

?>
-----
<?php

namespace RectorLaravel\Tests\Rector\Expr\SubStrToStartsWithOrEndsWithStaticMethodCallRector\Fixture;

if (\Illuminate\Support\Str::startsWith($value, 'a')) {
    return true;
}

if (\Illuminate\Support\Str::endsWith($value, 'a')) {
    return true;
}

if (\Illuminate\Support\Str::startsWith($value, 'a')) {
    return true;
}

if (\Illuminate\Support\Str::startsWith($value, $prefix)) {
    return true;
}

if (\Illuminate\Support\Str::startsWith($value, 'a')) {
    return true;
}

?>
