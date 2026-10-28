<?php

namespace RectorLaravel\Tests\Rector\Class_\RemoveDefaultAuthorizeTrueFromFormRequestRector\Source;

use Illuminate\Foundation\Http\FormRequest;

abstract class TraitAdminRequest extends FormRequest
{
    use AuthorizesWithPolicy;
}
