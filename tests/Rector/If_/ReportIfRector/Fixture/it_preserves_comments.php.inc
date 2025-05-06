<?php

namespace RectorLaravel\Tests\Rector\If_\ReportIfRector\Fixture;

class ItPreservesComments
{
    public function handle($condition)
    {
        /** Some comment */
        if ($condition) {
            report(new \Exception());
        }

        // Some comment
        if ($condition) {
            report(new \Exception());
        }

        if ($condition) {
            /** Some comment */
            report(new \Exception());
        }

        if ($condition) {
            // Some comment
            // Some comment
            report(new \Exception());
        }

        /** Some comment */
        if ($condition) {
            /** Some comment */
            report(new \Exception());
        }
    }
}

?>
-----
<?php

namespace RectorLaravel\Tests\Rector\If_\ReportIfRector\Fixture;

class ItPreservesComments
{
    public function handle($condition)
    {
        /** Some comment */
        report_if($condition, new \Exception());

        // Some comment
        report_if($condition, new \Exception());

        /** Some comment */
        report_if($condition, new \Exception());

        // Some comment
        // Some comment
        report_if($condition, new \Exception());

        /** Some comment */
        /** Some comment */
        report_if($condition, new \Exception());
    }
}

?>
