<?php

namespace UserLand;

require_once __DIR__ . '/../Illuminate/Support/Facades/Facade.php';

class SomeFacade extends \Illuminate\Support\Facades\Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return \UserLand\SomeService::class;
    }
}
