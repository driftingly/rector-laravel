<?php

namespace RectorLaravel\Tests\Rector\Class_\FailOnUnknownFieldsPropertyToFailOnUnknownFieldsAttributeRector\Fixture;

use Illuminate\Foundation\Http\FormRequest;

class StorePostRequest extends FormRequest
{
    protected $failOnUnknownFields = true;
}

?>
-----
<?php

namespace RectorLaravel\Tests\Rector\Class_\FailOnUnknownFieldsPropertyToFailOnUnknownFieldsAttributeRector\Fixture;

use Illuminate\Foundation\Http\FormRequest;

#[\Illuminate\Foundation\Http\Attributes\FailOnUnknownFields]
class StorePostRequest extends FormRequest
{
}

?>
