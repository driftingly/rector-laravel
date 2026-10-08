<?php

namespace RectorLaravel\Tests\Rector\ClassMethod\ReplaceTestAttributeWithPrefixedFunctionRector\Fixture;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class KeepOtherAttributes extends TestCase
{
    #[Test, Group('math')]
    public function it_adds_numbers(): void
    {
        $this->assertSame(2, 1 + 1);
    }

    #[Test]
    #[DataProvider('provideData')]
    public function it_uses_data(int $value): void
    {
        $this->assertIsInt($value);
    }
}

?>
-----
<?php

namespace RectorLaravel\Tests\Rector\ClassMethod\ReplaceTestAttributeWithPrefixedFunctionRector\Fixture;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class KeepOtherAttributes extends TestCase
{
    #[Group('math')]
    public function test_it_adds_numbers(): void
    {
        $this->assertSame(2, 1 + 1);
    }

    #[DataProvider('provideData')]
    public function test_it_uses_data(int $value): void
    {
        $this->assertIsInt($value);
    }
}

?>
