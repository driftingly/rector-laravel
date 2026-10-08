<?php

namespace RectorLaravel\Tests\Rector\Class_\RemoveDefaultAuthorizeTrueFromFormRequestRector\Fixture;

use Illuminate\Foundation\Http\FormRequest;

class SkipWithLogicRequest extends FormRequest
{
    public function authorize(): bool
    {
        $this->prepare();

        return true;
    }

    private function prepare(): void
    {
    }
}

?>
