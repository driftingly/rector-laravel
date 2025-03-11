<?php

namespace RectorLaravel\Tests\Rector\MethodCall\OrWhereToWhereAnyRector\Fixture;

use Illuminate\Contracts\Database\Query\Builder;

class SkipNestedQueries
{
    public function run(Builder $builder)
    {
        $users = $builder
            ->where('active', true)
            ->orWhere('type', 'admin')
            ->orWhere(function($query) {
                $query->where('role', 'admin')
                    ->orWhere('level', 'admin');
            })
            ->get();
    }
}
?>
