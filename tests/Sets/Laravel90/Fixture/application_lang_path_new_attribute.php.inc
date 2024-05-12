<?php

namespace RectorLaravel\Tests\Sets\Laravel90;

use Illuminate\Foundation\Application;

class SomeFixture
{
    protected Application $app;

    public function run(): void
    {
        $this->app->langPath();
    }
}

?>
