<?php

namespace RectorLaravel\Tests\Rector\MethodCall\RequestGetToRequestInputRector\Fixture;

use Illuminate\Foundation\Http\FormRequest;

class StorePostRequest extends FormRequest
{
    public function handle()
    {
        $name = $this->get('name');
        $name = $this->get('name', 'default');
    }
}
?>