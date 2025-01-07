<?php

namespace RectorLaravel\Tests\Rector\FuncCall\ArgumentFuncCallToMethodCallRector\Fixture;

class Back
{
    public function action()
    {
        return back();
    }

    public function actionWithParams()
    {
        return back(200);
    }
}

?>
-----
<?php

namespace RectorLaravel\Tests\Rector\FuncCall\ArgumentFuncCallToMethodCallRector\Fixture;

class Back
{
    public function __construct(private readonly \Illuminate\Routing\Redirector $redirector)
    {
    }
    public function action()
    {
        return $this->redirector->back();
    }

    public function actionWithParams()
    {
        return $this->redirector->back(200);
    }
}

?>
