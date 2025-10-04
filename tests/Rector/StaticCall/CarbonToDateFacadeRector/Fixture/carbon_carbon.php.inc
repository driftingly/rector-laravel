<?php

namespace RectorLaravel\Tests\Rector\StaticCall\CarbonToDateFacadeRector\Fixture;

use Carbon\Carbon;

class CarbonCarbon
{
    public function run()
    {
        $now = Carbon::now();
        $today = Carbon::today();
        $yesterday = Carbon::yesterday();
        $tomorrow = Carbon::tomorrow();
        $parsed = Carbon::parse('2024-01-01');
        $created = Carbon::create(2024, 1, 1);
    }
}

?>
-----
<?php

namespace RectorLaravel\Tests\Rector\StaticCall\CarbonToDateFacadeRector\Fixture;

use Carbon\Carbon;

class CarbonCarbon
{
    public function run()
    {
        $now = \Illuminate\Support\Facades\Date::now();
        $today = \Illuminate\Support\Facades\Date::today();
        $yesterday = \Illuminate\Support\Facades\Date::yesterday();
        $tomorrow = \Illuminate\Support\Facades\Date::tomorrow();
        $parsed = \Illuminate\Support\Facades\Date::parse('2024-01-01');
        $created = \Illuminate\Support\Facades\Date::create(2024, 1, 1);
    }
}

?>
