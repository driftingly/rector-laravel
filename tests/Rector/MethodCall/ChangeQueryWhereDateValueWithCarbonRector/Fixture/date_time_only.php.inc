<?php

namespace RectorLaravel\Tests\Rector\MethodCall\ChangeQueryWhereDateValueWithCarbonRector\Fixture;

use Illuminate\Database\Query\Builder;

class DateTimeOnly
{
    public function run(Builder $query, \DateTimeInterface $dateTime)
    {
        $query->whereDate('created_at', '>', $dateTime);
    }
}

?>
-----
<?php

namespace RectorLaravel\Tests\Rector\MethodCall\ChangeQueryWhereDateValueWithCarbonRector\Fixture;

use Illuminate\Database\Query\Builder;

class DateTimeOnly
{
    public function run(Builder $query, \DateTimeInterface $dateTime)
    {
        $query->whereDate('created_at', '>=', $dateTime);
        $query->whereTime('created_at', '>=', $dateTime);
    }
}

?>
