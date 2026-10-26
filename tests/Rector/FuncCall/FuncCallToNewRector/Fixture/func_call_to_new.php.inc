<?php

namespace RectorLaravel\Tests\Rector\FuncCall\FuncCallToNewRector\Fixture;

class FuncCallToNew
{
    public function run()
    {
        $empty = collect();

        return Collect([1, 2, 3]);
    }
}

?>
-----
<?php

namespace RectorLaravel\Tests\Rector\FuncCall\FuncCallToNewRector\Fixture;

class FuncCallToNew
{
    public function run()
    {
        $empty = new \Illuminate\Support\Collection();

        return new \Illuminate\Support\Collection([1, 2, 3]);
    }
}

?>
