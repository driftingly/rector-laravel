<?php

namespace RectorLaravel\Tests\Rector\StaticCall\RequestStaticValidateToInjectRector\Fixture;

use Illuminate\Foundation\Http\FormRequest;

final class AppServiceProvider
{
    public function boot(): void
    {
        FormRequest::failOnUnknownFields();
    }
}

?>
-----
<?php

namespace RectorLaravel\Tests\Rector\StaticCall\RequestStaticValidateToInjectRector\Fixture;

use Illuminate\Foundation\Http\FormRequest;

final class AppServiceProvider
{
    public function boot(): void
    {
        FormRequest::failOnUnknownFields();
    }
}

?>
