<?php

namespace RectorLaravel\Tests\Rector\FuncCall\FilledAndBlankToTypedComparisonRector\Fixture;

function test(array $array)
{
    filled($array);
    blank($array);
}

function skip(array|string $array)
{
    filled($array);
    blank($array);
}

?>
-----
<?php

namespace RectorLaravel\Tests\Rector\FuncCall\FilledAndBlankToTypedComparisonRector\Fixture;

function test(array $array)
{
    $array !== [];
    $array === [];
}

function skip(array|string $array)
{
    filled($array);
    blank($array);
}

?>
