<?php

namespace RectorLaravel\Tests\Rector\StaticCall\CarbonToDateFacadeRector\Fixture;

use Carbon\Carbon;

class WithFullyQualifiedNames
{
    public function run()
    {
        $now = \Carbon\Carbon::now();
        $today = \Illuminate\Support\Carbon::today();
    }
}

?>
-----
<?php

namespace RectorLaravel\Tests\Rector\StaticCall\CarbonToDateFacadeRector\Fixture;

use Carbon\Carbon;

class WithFullyQualifiedNames
{
    public function run()
    {
        $now = \Illuminate\Support\Facades\Date::now();
        $today = \Illuminate\Support\Facades\Date::today();
    }
}

?>
