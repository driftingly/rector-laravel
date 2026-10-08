<?php

namespace RectorLaravel\Tests\Rector\Class_\RemoveDefaultAuthorizeTrueFromFormRequestRector\Fixture;

use Illuminate\Foundation\Http\FormRequest;

class SkipReturnFalseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return false;
    }
}

?>
