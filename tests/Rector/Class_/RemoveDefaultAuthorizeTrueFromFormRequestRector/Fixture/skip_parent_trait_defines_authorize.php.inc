<?php

namespace RectorLaravel\Tests\Rector\Class_\RemoveDefaultAuthorizeTrueFromFormRequestRector\Fixture;

use RectorLaravel\Tests\Rector\Class_\RemoveDefaultAuthorizeTrueFromFormRequestRector\Source\TraitAdminRequest;

class SkipParentTraitDefinesAuthorizeRequest extends TraitAdminRequest
{
    public function authorize(): bool
    {
        return true;
    }
}

?>
