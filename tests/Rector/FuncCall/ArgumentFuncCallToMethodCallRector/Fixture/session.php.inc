<?php

namespace RectorLaravel\Tests\Rector\FuncCall\FactoryFuncCallToStaticCallRector\Fixture;

class Session
{
    public function action()
    {
        $session = session();
        session(['key']);
        session('key', 'value');
    }
}

?>
-----
<?php

namespace RectorLaravel\Tests\Rector\FuncCall\FactoryFuncCallToStaticCallRector\Fixture;

class Session
{
    public function __construct(private readonly \Illuminate\Session\SessionManager $sessionManager)
    {
    }
    public function action()
    {
        $session = $this->sessionManager;
        $this->sessionManager->put(['key']);
        $this->sessionManager->get('key', 'value');
    }
}

?>
