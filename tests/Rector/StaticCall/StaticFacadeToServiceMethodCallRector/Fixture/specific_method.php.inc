<?php

namespace RectorLaravel\Tests\Rector\StaticCall\StaticFacadeToServiceMethodCallRector\Fixture;

class SpecificMethod
{
    public function run()
    {
        return \Illuminate\Support\Facades\DB::table('users');
    }
}

?>
-----
<?php

namespace RectorLaravel\Tests\Rector\StaticCall\StaticFacadeToServiceMethodCallRector\Fixture;

class SpecificMethod
{
    public function __construct(private readonly \Illuminate\Database\DatabaseManager $databaseManager)
    {
    }
    public function run()
    {
        return $this->databaseManager->table('users');
    }
}

?>
