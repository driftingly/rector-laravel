<?php

namespace RectorLaravel\Tests\Rector\ClassMethod\ReplaceTestAttributeWithPrefixedFunctionRector\Fixture;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class RemoveRedundantAttributeOnPrefixedMethod extends TestCase
{
    #[Test]
    public function test_it_works(): void
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

class RemoveRedundantAttributeOnPrefixedMethod extends TestCase
{
    public function test_it_works(): void
    {
        $this->assertTrue(true);
    }
}

?>
