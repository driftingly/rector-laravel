<?php

namespace RectorLaravel\Tests\Rector\Class_\ErrorBagPropertyToErrorBagAttributeRector\Fixture;

use Illuminate\Foundation\Http\FormRequest;

#[\Illuminate\Foundation\Http\Attributes\ErrorBag('custom')]
class SkipExistingAttribute extends FormRequest
{
    protected $errorBag = 'custom';
}

?>
