<?php

namespace RectorLaravel\Tests\Rector\Class_\FailOnUnknownFieldsPropertyToFailOnUnknownFieldsAttributeRector\Fixture;

use Illuminate\Foundation\Http\FormRequest;

class SkipFailOnUnknownFieldsFalse extends FormRequest
{
    protected $failOnUnknownFields = false;
}

?>
