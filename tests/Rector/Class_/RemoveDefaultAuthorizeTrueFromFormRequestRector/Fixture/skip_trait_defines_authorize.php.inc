<?php

namespace RectorLaravel\Tests\Rector\Class_\RemoveDefaultAuthorizeTrueFromFormRequestRector\Fixture;

use Illuminate\Foundation\Http\FormRequest;
use RectorLaravel\Tests\Rector\Class_\RemoveDefaultAuthorizeTrueFromFormRequestRector\Source\AuthorizesWithPolicy;

class SkipTraitDefinesAuthorizeRequest extends FormRequest
{
    use AuthorizesWithPolicy;

    public function authorize(): bool
    {
        return true;
    }
}

?>
