<?php

namespace RectorLaravel\Tests\Rector\MethodCall\OrWhereToWhereAnyRector\Fixture;

use Illuminate\Contracts\Database\Query\Builder;

class SkipClosure
{
    public function run(Builder $builder)
    {
        $users = $builder
            ->where('active', true)
            ->orWhere(function($query) {
                $query->where('type', 'admin');
            })
            ->get();
    }
}
?>
