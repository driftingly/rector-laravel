<?php

namespace RectorLaravel\Tests\Rector\If_\ThrowIfRector\Fixture;

class Fixture
{
    public function handle($condition)
    {
        if ($condition) {
            throw new Exception();
        }
        if (!$condition) {
            throw new Exception();
        }
        if ($condition = call()) {
            throw new Exception();
        }
        if ($condition) {
            throw new Exception($condition);
        }
    }
}

?>
-----
<?php

namespace RectorLaravel\Tests\Rector\If_\ThrowIfRector\Fixture;

class Fixture
{
    public function handle($condition)
    {
        throw_if($condition, new Exception());
        throw_unless($condition, new Exception());
        throw_if($condition = call(), new Exception());
        throw_if($condition, new Exception($condition));
    }
}

?>
