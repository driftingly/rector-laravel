<?php

namespace RectorLaravel\Tests\Rector\Class_\StopOnFirstFailurePropertyToStopOnFirstFailureAttributeRector\Fixture;

use Illuminate\Foundation\Http\FormRequest;

class SkipStopOnFirstFailureString extends FormRequest
{
    protected $stopOnFirstFailure = 'true';
}
