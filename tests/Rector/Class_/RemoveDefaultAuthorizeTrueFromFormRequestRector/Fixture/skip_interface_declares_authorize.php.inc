<?php

namespace RectorLaravel\Tests\Rector\Class_\RemoveDefaultAuthorizeTrueFromFormRequestRector\Fixture;

use Illuminate\Foundation\Http\FormRequest;
use RectorLaravel\Tests\Rector\Class_\RemoveDefaultAuthorizeTrueFromFormRequestRector\Source\AuthorizableRequest;

class SkipInterfaceDeclaresAuthorizeRequest extends FormRequest implements AuthorizableRequest
{
    public function authorize(): bool
    {
        return true;
    }
}

?>
