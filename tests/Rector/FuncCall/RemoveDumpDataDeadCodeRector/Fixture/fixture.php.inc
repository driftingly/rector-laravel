<?php

namespace Rector\Laravel\Tests\Rector\FuncCall\RemoveDumpDataDeadCodeRector\Fixture;

class Fixture
{
    public function store()
    {
        dd('test');
        return true;
    }

    public function update()
    {
        dump('test');
        return true;
    }
}

?>
-----
<?php

namespace Rector\Laravel\Tests\Rector\FuncCall\RemoveDumpDataDeadCodeRector\Fixture;

class Fixture
{
    public function store()
    {
        return true;
    }

    public function update()
    {
        return true;
    }
}

?>
