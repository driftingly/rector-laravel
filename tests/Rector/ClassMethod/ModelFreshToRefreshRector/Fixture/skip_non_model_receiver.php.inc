<?php

namespace RectorLaravel\Tests\Rector\ClassMethod\ModelFreshToRefreshRector\Fixture;

use Illuminate\Database\Eloquent\Collection;
use PHPUnit\Framework\TestCase;
use UserLand\SomeModel;

final class SkipNonModelReceiverTest extends TestCase
{
    private Collection $collection;

    public function testItLeavesACollectionReceiverAlone(): void
    {
        $collection = $this->collection;

        $this->assertSame($this->collection, $collection->fresh());
    }
}
