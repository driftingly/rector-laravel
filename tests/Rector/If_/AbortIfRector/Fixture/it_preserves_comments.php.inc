<?php

namespace RectorLaravel\Tests\Rector\If_\AbortIfRector\Fixture;

class ItPreservesComments
{
    public function handle($condition)
    {
        /** Some comment */
        if ($condition) {
            abort(404);
        }

        // Some comment
        if ($condition) {
            abort(404);
        }

        if ($condition) {
            /** Some comment */
            abort(404);
        }

        if ($condition) {
            // Some comment
            // Some comment
            abort(404);
        }

        /** Some comment */
        if ($condition) {
            /** Some comment */
            abort(404);
        }
    }
}

?>
-----
<?php

namespace RectorLaravel\Tests\Rector\If_\AbortIfRector\Fixture;

class ItPreservesComments
{
    public function handle($condition)
    {
        /** Some comment */
        abort_if($condition, 404);

        // Some comment
        abort_if($condition, 404);

        /** Some comment */
        abort_if($condition, 404);

        // Some comment
        // Some comment
        abort_if($condition, 404);

        /** Some comment */
        /** Some comment */
        abort_if($condition, 404);
    }
}

?>
