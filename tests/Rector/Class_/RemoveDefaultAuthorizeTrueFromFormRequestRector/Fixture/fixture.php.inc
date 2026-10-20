<?php

namespace RectorLaravel\Tests\Rector\Class_\RemoveDefaultAuthorizeTrueFromFormRequestRector\Fixture;

use Illuminate\Foundation\Http\FormRequest;

class StorePostRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [];
    }
}

?>
-----
<?php

namespace RectorLaravel\Tests\Rector\Class_\RemoveDefaultAuthorizeTrueFromFormRequestRector\Fixture;

use Illuminate\Foundation\Http\FormRequest;

class StorePostRequest extends FormRequest
{
    public function rules(): array
    {
        return [];
    }
}

?>
