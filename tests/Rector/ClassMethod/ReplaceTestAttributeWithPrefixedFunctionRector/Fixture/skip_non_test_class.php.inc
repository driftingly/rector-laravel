<?php

namespace RectorLaravel\Tests\Rector\ClassMethod\ReplaceTestAttributeWithPrefixedFunctionRector\Fixture;

use PHPUnit\Framework\Attributes\Test;

class SkipNonTestClass
{
    #[Test]
    public function it_works(): void
    {
    }
}

?>
