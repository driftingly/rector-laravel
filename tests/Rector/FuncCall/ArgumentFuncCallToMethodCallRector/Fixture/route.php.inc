<?php

namespace RectorLaravel\Tests\Rector\FuncCall\ArgumentFuncCallToMethodCallRector\Fixture;

class Route
{
    public function action()
    {
        return route('template.blade');
    }
}

?>
-----
<?php

namespace RectorLaravel\Tests\Rector\FuncCall\ArgumentFuncCallToMethodCallRector\Fixture;

class Route
{
    public function __construct(private \Illuminate\Routing\UrlGenerator $urlGenerator)
    {
    }
    public function action()
    {
        return $this->urlGenerator->route('template.blade');
    }
}

?>
