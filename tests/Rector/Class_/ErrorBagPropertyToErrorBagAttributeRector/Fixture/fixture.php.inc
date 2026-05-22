<?php

namespace RectorLaravel\Tests\Rector\Class_\ErrorBagPropertyToErrorBagAttributeRector\Fixture;

use Illuminate\Foundation\Http\FormRequest;

class StorePostRequest extends FormRequest
{
    protected $errorBag = 'custom';
}

?>
-----
<?php

namespace RectorLaravel\Tests\Rector\Class_\ErrorBagPropertyToErrorBagAttributeRector\Fixture;

use Illuminate\Foundation\Http\FormRequest;

#[\Illuminate\Foundation\Http\Attributes\ErrorBag('custom')]
class StorePostRequest extends FormRequest
{
}

?>
