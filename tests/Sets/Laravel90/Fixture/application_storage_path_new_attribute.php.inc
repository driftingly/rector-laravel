<?php

namespace RectorLaravel\Tests\Sets\Laravel90;

use Illuminate\Contracts\Foundation\Application;

class SomeFixture
{
    protected Application $app;

    public function run(): void
    {
        $this->app->storagePath();
    }
}

?>
