<?php

namespace RectorLaravel\Tests\Rector\StaticCall\StaticFacadeToServiceMethodCallRector\Fixture;

class SkipUnconfigured
{
    public function run()
    {
        // method not configured for DB (only "table" is mapped)
        return \Illuminate\Support\Facades\DB::raw('count(*)');
    }
}
