<?php

namespace RectorLaravel\Tests\Rector\FuncCall\ArgumentFuncCallToMethodCallRector\Fixture;

class View
{
    public function action()
    {
        $template = view('template.blade');
        $viewFactory = view();
    }
}

?>
-----
<?php

namespace RectorLaravel\Tests\Rector\FuncCall\ArgumentFuncCallToMethodCallRector\Fixture;

class View
{
    public function __construct(private readonly \Illuminate\Contracts\View\Factory $factory)
    {
    }
    public function action()
    {
        $template = $this->factory->make('template.blade');
        $viewFactory = $this->factory;
    }
}

?>
