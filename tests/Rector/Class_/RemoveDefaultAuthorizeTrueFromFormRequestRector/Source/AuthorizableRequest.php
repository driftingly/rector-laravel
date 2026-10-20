<?php

namespace RectorLaravel\Tests\Rector\Class_\RemoveDefaultAuthorizeTrueFromFormRequestRector\Source;

interface AuthorizableRequest
{
    public function authorize(): bool;
}
