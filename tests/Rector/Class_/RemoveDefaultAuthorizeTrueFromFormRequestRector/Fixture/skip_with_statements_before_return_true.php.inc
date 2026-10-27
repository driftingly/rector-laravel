<?php

namespace RectorLaravel\Tests\Rector\Class_\RemoveDefaultAuthorizeTrueFromFormRequestRector\Fixture;

use Illuminate\Foundation\Http\FormRequest;

class SkipWithStatementsBeforeReturnTrueRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();

        throw_if($user === null, new \RuntimeException('Unauthenticated.'));

        return true;
    }
}

?>
