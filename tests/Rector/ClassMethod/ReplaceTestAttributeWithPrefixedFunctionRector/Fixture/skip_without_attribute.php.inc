<?php

namespace RectorLaravel\Tests\Rector\ClassMethod\ReplaceTestAttributeWithPrefixedFunctionRector\Fixture;

use PHPUnit\Framework\TestCase;

class SkipWithoutAttribute extends TestCase
{
    public function helper(): void
    {
    }
}

?>
