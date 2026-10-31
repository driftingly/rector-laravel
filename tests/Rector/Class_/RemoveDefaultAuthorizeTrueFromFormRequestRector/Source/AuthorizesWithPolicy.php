<?php

namespace RectorLaravel\Tests\Rector\Class_\RemoveDefaultAuthorizeTrueFromFormRequestRector\Source;

trait AuthorizesWithPolicy
{
    public function authorize(): bool
    {
        return false;
    }
}
