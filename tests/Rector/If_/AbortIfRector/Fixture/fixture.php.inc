<?php

namespace RectorLaravel\Tests\Rector\If_\AbortIfRector\Fixture;

class Fixture
{
    public function handle($condition)
    {
        if ($condition) {
            abort(404);
        }
        if (!$condition) {
            abort(404);
        }
        if ($condition = call()) {
            abort(404);
        }
        if ($condition) {
            abort(404);
        }
    }
}

?>
-----
<?php

namespace RectorLaravel\Tests\Rector\If_\AbortIfRector\Fixture;

class Fixture
{
    public function handle($condition)
    {
        abort_if($condition, 404);
        abort_unless($condition, 404);
        abort_if($condition = call(), 404);
        abort_if($condition, 404);
    }
}

?>
