<?php

namespace RectorLaravel\Tests\Rector\FuncCall\FilledAndBlankToTypedComparisonRector\Fixture;

use RectorLaravel\Tests\Rector\FuncCall\FilledAndBlankToTypedComparisonRector\Source\SomeClass;
use RectorLaravel\Tests\Rector\FuncCall\FilledAndBlankToTypedComparisonRector\Source\SomeOtherClass;

function test(?SomeClass $class)
{
    filled($class);
    blank($class);
}

function testTwo(SomeClass|null $class)
{
    filled($class);
    blank($class);
}

function testThree(SomeClass|SomeOtherClass|null $class)
{
    filled($class);
    blank($class);
}

?>
-----
<?php

namespace RectorLaravel\Tests\Rector\FuncCall\FilledAndBlankToTypedComparisonRector\Fixture;

use RectorLaravel\Tests\Rector\FuncCall\FilledAndBlankToTypedComparisonRector\Source\SomeClass;
use RectorLaravel\Tests\Rector\FuncCall\FilledAndBlankToTypedComparisonRector\Source\SomeOtherClass;

function test(?SomeClass $class)
{
    $class instanceof \RectorLaravel\Tests\Rector\FuncCall\FilledAndBlankToTypedComparisonRector\Source\SomeClass;
    !$class instanceof \RectorLaravel\Tests\Rector\FuncCall\FilledAndBlankToTypedComparisonRector\Source\SomeClass;
}

function testTwo(SomeClass|null $class)
{
    $class instanceof \RectorLaravel\Tests\Rector\FuncCall\FilledAndBlankToTypedComparisonRector\Source\SomeClass;
    !$class instanceof \RectorLaravel\Tests\Rector\FuncCall\FilledAndBlankToTypedComparisonRector\Source\SomeClass;
}

function testThree(SomeClass|SomeOtherClass|null $class)
{
    filled($class);
    blank($class);
}

?>
