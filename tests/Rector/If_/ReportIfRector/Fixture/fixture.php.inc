<?php

namespace RectorLaravel\Tests\Rector\If_\ReportIfRector\Fixture;

class Fixture
{
    public function handle($condition)
    {
        if ($condition) {
            report(new \Exception());
        }
        if (!$condition) {
            report(new \Exception());
        }
        if ($condition = call()) {
            report(new \Exception());
        }
        if ($condition) {
            report(new \Exception());
        }
    }
}

?>
-----
<?php

namespace RectorLaravel\Tests\Rector\If_\ReportIfRector\Fixture;

class Fixture
{
    public function handle($condition)
    {
        report_if($condition, new \Exception());
        report_unless($condition, new \Exception());
        report_if($condition = call(), new \Exception());
        report_if($condition, new \Exception());
    }
}

?>
