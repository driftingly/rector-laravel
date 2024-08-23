<?php

namespace RectorLaravel\Tests\Sets\Laravel90\Fixture;

use Illuminate\Foundation\Application;

class CustomApplication extends Application
{
    public function storagePath(): string
    {
        return '';
    }

}

$customApp = new CustomApplication();
$customApp->storagePath();

?>
-----
<?php

namespace RectorLaravel\Tests\Sets\Laravel90\Fixture;

use Illuminate\Foundation\Application;

class CustomApplication extends Application
{
    public function storagePath($path = ''): string
    {
        return '';
    }

}

$customApp = new CustomApplication();
$customApp->storagePath();

?>
