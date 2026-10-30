<?php

namespace RectorLaravel\Tests\Rector\ArrayDimFetch\ServerVariableToRequestFacadeRector\Fixture;

function serverName()
{
    return $_SERVER['SERVER_NAME'];
}

$closure = function () {
    return $_SERVER['SERVER_NAME'];
};

$anonymousClass = new class
{
    public function serverName()
    {
        return $_SERVER['SERVER_NAME'];
    }
};

class ReturnInsideFunctionLike
{
    public function serverName()
    {
        return $_SERVER['SERVER_NAME'];
    }
}

?>
-----
<?php

namespace RectorLaravel\Tests\Rector\ArrayDimFetch\ServerVariableToRequestFacadeRector\Fixture;

function serverName()
{
    return \Illuminate\Support\Facades\Request::server('SERVER_NAME');
}

$closure = function () {
    return \Illuminate\Support\Facades\Request::server('SERVER_NAME');
};

$anonymousClass = new class
{
    public function serverName()
    {
        return \Illuminate\Support\Facades\Request::server('SERVER_NAME');
    }
};

class ReturnInsideFunctionLike
{
    public function serverName()
    {
        return \Illuminate\Support\Facades\Request::server('SERVER_NAME');
    }
}

?>
