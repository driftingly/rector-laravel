<?php

namespace RectorLaravel\Tests\Rector\Class_\RemoveDefaultAuthorizeTrueFromFormRequestRector\Source;

use Illuminate\Foundation\Http\FormRequest;

abstract class AdminRequest extends FormRequest
{
    public function authorize(): bool
    {
        return false;
    }
}
