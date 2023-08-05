<?php

namespace RectorLaravel\Tests\Rector\FuncCall\ArgumentFuncCallToMethodCallRector\Fixture;

class Auth
{
    public function action()
    {
        return auth();
    }
}

?>
-----
<?php

namespace RectorLaravel\Tests\Rector\FuncCall\ArgumentFuncCallToMethodCallRector\Fixture;

class Auth
{
    public function __construct(private \Illuminate\Contracts\Auth\Guard $guard)
    {
    }
    public function action()
    {
        return $this->guard;
    }
}

?>
