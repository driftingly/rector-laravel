<?php

namespace RectorLaravel\Tests\Rector\If_\ThrowIfRector\Fixture;

class ItPreservesComments
{
    public function handle($condition)
    {
        /** Some comment */
        if ($condition) {
            throw new Exception();
        }

        // Some comment
        if ($condition) {
            throw new Exception();
        }

        if ($condition) {
            /** Some comment */
            throw new Exception();
        }

        if ($condition) {
            // Some comment
            // Some comment
            throw new Exception();
        }

        /** Some comment */
        if ($condition) {
            /** Some comment */
            throw new Exception();
        }
    }
}

?>
-----
<?php

namespace RectorLaravel\Tests\Rector\If_\ThrowIfRector\Fixture;

class ItPreservesComments
{
    public function handle($condition)
    {
        /** Some comment */
        throw_if($condition, new Exception());

        // Some comment
        throw_if($condition, new Exception());

        /** Some comment */
        throw_if($condition, new Exception());

        // Some comment
        // Some comment
        throw_if($condition, new Exception());

        /** Some comment */
        /** Some comment */
        throw_if($condition, new Exception());
    }
}

?>
