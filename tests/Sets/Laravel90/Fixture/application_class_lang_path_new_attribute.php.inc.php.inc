<?php

namespace RectorLaravel\Tests\Sets\Laravel90\Fixture;

use Illuminate\Foundation\Application;

class CustomApplication extends Application
{
    public function langPath(): string
    {
        return '';
    }

}

$customApp = new CustomApplication();
$customApp->langPath();

?>
-----
<?php

namespace RectorLaravel\Tests\Sets\Laravel90\Fixture;

use Illuminate\Foundation\Application;

class CustomApplication extends Application
{
    public function langPath($path = ''): string
    {
        return '';
    }

}

$customApp = new CustomApplication();
$customApp->langPath();

?>
