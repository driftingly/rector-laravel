<?php

namespace RectorLaravel\Tests\Rector\StaticCall\StaticFacadeToServiceMethodCallRector\Fixture;

class InjectAllMethods
{
    public function run()
    {
        return \Illuminate\Support\Facades\Auth::user();
    }
}

?>
-----
<?php

namespace RectorLaravel\Tests\Rector\StaticCall\StaticFacadeToServiceMethodCallRector\Fixture;

class InjectAllMethods
{
    public function __construct(private readonly \Illuminate\Auth\AuthManager $authManager)
    {
    }
    public function run()
    {
        return $this->authManager->user();
    }
}

?>
