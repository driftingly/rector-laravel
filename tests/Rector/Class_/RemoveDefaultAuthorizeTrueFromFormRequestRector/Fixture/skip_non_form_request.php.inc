<?php

namespace RectorLaravel\Tests\Rector\Class_\RemoveDefaultAuthorizeTrueFromFormRequestRector\Fixture;

class SkipNonFormRequest
{
    public function authorize(): bool
    {
        return true;
    }
}

?>
