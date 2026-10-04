<?php

namespace RectorLaravel\Tests\Rector\Class_\RemoveDefaultAuthorizeTrueFromFormRequestRector\Fixture;

use RectorLaravel\Tests\Rector\Class_\RemoveDefaultAuthorizeTrueFromFormRequestRector\Source\AdminRequest;

class SkipParentDefinesAuthorizeRequest extends AdminRequest
{
    public function authorize(): bool
    {
        return true;
    }
}

?>
