<?php

namespace RectorLaravel\Tests\Rector\MethodCall\AssertStatusToAssertMethodRector\Fixture;

use Illuminate\Contracts\Database\Query\Builder;

class SkipPossiblyNullValue
{
    public function test(Builder $query, ?int $userId, mixed $value)
    {
        $query
            ->where('user_id', $userId)
            ->where('foo', '=', $value)
            ->where('bar', '!=', $userId);
    }
}
?>
