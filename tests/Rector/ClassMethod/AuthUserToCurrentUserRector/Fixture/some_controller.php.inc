<?php

namespace RectorLaravel\Tests\Rector\ClassMethod\AuthUserToCurrentUserRector\Fixture;

class SomeController
{
    public function store()
    {
        return auth()->user();
    }
}

?>
-----
<?php

namespace RectorLaravel\Tests\Rector\ClassMethod\AuthUserToCurrentUserRector\Fixture;

class SomeController
{
    public function store(#[\Illuminate\Container\Attributes\CurrentUser]
    \RectorLaravel\Tests\Rector\ClassMethod\AuthUserToCurrentUserRector\Source\User $user)
    {
        return $user;
    }
}

?>
