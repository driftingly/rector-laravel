<?php

namespace RectorLaravel\Tests\Rector\Class_\RemoveDefaultAuthorizeTrueFromFormRequestRector\Fixture;

use Illuminate\Foundation\Http\FormRequest;

class SkipStatementBeforeReturnRequest extends FormRequest
{
    public function authorize(): bool
    {
        logger()->info('authorizing');

        return true;
    }
}

?>
