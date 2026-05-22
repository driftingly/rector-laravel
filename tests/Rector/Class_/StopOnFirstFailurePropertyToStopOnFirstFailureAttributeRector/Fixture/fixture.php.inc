<?php

namespace RectorLaravel\Tests\Rector\Class_\StopOnFirstFailurePropertyToStopOnFirstFailureAttributeRector\Fixture;

use Illuminate\Foundation\Http\FormRequest;

class StorePostRequest extends FormRequest
{
    protected $stopOnFirstFailure = true;
}

?>
-----
<?php

namespace RectorLaravel\Tests\Rector\Class_\StopOnFirstFailurePropertyToStopOnFirstFailureAttributeRector\Fixture;

use Illuminate\Foundation\Http\FormRequest;

#[\Illuminate\Foundation\Http\Attributes\StopOnFirstFailure]
class StorePostRequest extends FormRequest
{
}

?>
