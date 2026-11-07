<?php

namespace RectorLaravel\Tests\Rector\ClassMethod\ModelFreshToRefreshRector\Fixture;

use PHPUnit\Framework\TestCase;
use UserLand\SomeModel;

final class DynamicMethodNameCallTest extends TestCase
{
    public function testIt(): void
    {
        $model = SomeModel::create();
        $this->{'assert' . 'Same'}('done', $model->fresh()->status);
    }
}
-----
<?php

namespace RectorLaravel\Tests\Rector\ClassMethod\ModelFreshToRefreshRector\Fixture;

use PHPUnit\Framework\TestCase;
use UserLand\SomeModel;

final class DynamicMethodNameCallTest extends TestCase
{
    public function testIt(): void
    {
        $model = SomeModel::create();
        $this->{'assert' . 'Same'}('done', $model->refresh()->status);
    }
}
