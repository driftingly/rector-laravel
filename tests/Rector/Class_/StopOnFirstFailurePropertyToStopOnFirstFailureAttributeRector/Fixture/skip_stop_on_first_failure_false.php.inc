<?php

namespace RectorLaravel\Tests\Rector\Class_\StopOnFirstFailurePropertyToStopOnFirstFailureAttributeRector\Fixture;

use Illuminate\Foundation\Http\FormRequest;

class SkipStopOnFirstFailureFalse extends FormRequest
{
    protected $stopOnFirstFailure = false;
}

?>
