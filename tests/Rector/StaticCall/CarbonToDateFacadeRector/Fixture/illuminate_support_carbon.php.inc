<?php

namespace RectorLaravel\Tests\Rector\StaticCall\CarbonToDateFacadeRector\Fixture;

use Illuminate\Support\Carbon;

class IlluminateSupportCarbon
{
    public function run()
    {
        $now = Carbon::now();
        $today = Carbon::today();
        $parsed = Carbon::parse('2024-01-01');
        $created = Carbon::createFromFormat('Y-m-d', '2024-01-01');
    }
}

?>
-----
<?php

namespace RectorLaravel\Tests\Rector\StaticCall\CarbonToDateFacadeRector\Fixture;

use Illuminate\Support\Carbon;

class IlluminateSupportCarbon
{
    public function run()
    {
        $now = \Illuminate\Support\Facades\Date::now();
        $today = \Illuminate\Support\Facades\Date::today();
        $parsed = \Illuminate\Support\Facades\Date::parse('2024-01-01');
        $created = \Illuminate\Support\Facades\Date::createFromFormat('Y-m-d', '2024-01-01');
    }
}

?>
