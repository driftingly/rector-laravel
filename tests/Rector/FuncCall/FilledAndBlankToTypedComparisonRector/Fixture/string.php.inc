<?php

namespace RectorLaravel\Tests\Rector\FuncCall\FilledAndBlankToTypedComparisonRector\Fixture;

function test(string $string)
{
    filled($string);
    blank($string);
}

?>
-----
<?php

namespace RectorLaravel\Tests\Rector\FuncCall\FilledAndBlankToTypedComparisonRector\Fixture;

function test(string $string)
{
    $string !== '';
    $string === '';
}

?>
