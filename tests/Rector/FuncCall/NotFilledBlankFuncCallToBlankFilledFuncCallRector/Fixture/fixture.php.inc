<?php

namespace RectorLaravel\Tests\Rector\FuncCall\NotFilledBlankFuncCallToBlankFilledFuncCallRector\Fixture;

class Fixture
{
    public function run()
    {
        !filled([]);
        !blank([]);
    }
}

?>
-----
<?php

namespace RectorLaravel\Tests\Rector\FuncCall\NotFilledBlankFuncCallToBlankFilledFuncCallRector\Fixture;

class Fixture
{
    public function run()
    {
        blank([]);
        filled([]);
    }
}

?>
