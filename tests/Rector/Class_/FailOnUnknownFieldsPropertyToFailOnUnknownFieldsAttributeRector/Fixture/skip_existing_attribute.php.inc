<?php

namespace RectorLaravel\Tests\Rector\Class_\FailOnUnknownFieldsPropertyToFailOnUnknownFieldsAttributeRector\Fixture;

use Illuminate\Foundation\Http\FormRequest;

#[\Illuminate\Foundation\Http\Attributes\FailOnUnknownFields]
class SkipExistingAttribute extends FormRequest
{
    protected $failOnUnknownFields = true;
}

?>
