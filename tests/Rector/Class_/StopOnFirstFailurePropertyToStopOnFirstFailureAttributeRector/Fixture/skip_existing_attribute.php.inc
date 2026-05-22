<?php

namespace RectorLaravel\Tests\Rector\Class_\StopOnFirstFailurePropertyToStopOnFirstFailureAttributeRector\Fixture;

use Illuminate\Foundation\Http\FormRequest;

#[\Illuminate\Foundation\Http\Attributes\StopOnFirstFailure]
class SkipExistingAttribute extends FormRequest
{
    protected $stopOnFirstFailure = true;
}

?>
