<?php

namespace RectorLaravel\Tests\Sets\Laravel90\Fixture;

use Illuminate\Foundation\Exceptions\Handler;

class CustomHandler extends Handler
{
    protected function ignore(string $class)
    {
    }
}

?>
-----
<?php

namespace RectorLaravel\Tests\Sets\Laravel90\Fixture;

use Illuminate\Foundation\Exceptions\Handler;

class CustomHandler extends Handler
{
    public function ignore(string $class)
    {
    }
}

?>
