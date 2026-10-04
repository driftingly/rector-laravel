<?php

namespace RectorLaravel\Tests\Rector\ClassMethod\ReplaceTestAttributeWithPrefixedFunctionRector\Fixture;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class Fixture extends TestCase
{
    #[Test]
    public function onePlusOneShouldBeTwo(): void
    {
        $this->assertSame(2, 1 + 1);
    }

    #[Test]
    public function it_adds_numbers(): void
    {
        $this->assertSame(2, 1 + 1);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function fullyQualified(): void
    {
        $this->assertTrue(true);
    }
}

?>
-----
<?php

namespace RectorLaravel\Tests\Rector\ClassMethod\ReplaceTestAttributeWithPrefixedFunctionRector\Fixture;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class Fixture extends TestCase
{
    public function testOnePlusOneShouldBeTwo(): void
    {
        $this->assertSame(2, 1 + 1);
    }

    public function test_it_adds_numbers(): void
    {
        $this->assertSame(2, 1 + 1);
    }

    public function testFullyQualified(): void
    {
        $this->assertTrue(true);
    }
}

?>
