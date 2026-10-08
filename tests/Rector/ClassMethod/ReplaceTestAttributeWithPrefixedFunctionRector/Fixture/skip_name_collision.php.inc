<?php

namespace RectorLaravel\Tests\Rector\ClassMethod\ReplaceTestAttributeWithPrefixedFunctionRector\Fixture;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class SkipNameCollision extends TestCase
{
    #[Test]
    public function it_works(): void
    {
        $this->assertTrue(true);
    }

    public function test_it_works(): void
    {
        $this->assertTrue(true);
    }
}

?>
