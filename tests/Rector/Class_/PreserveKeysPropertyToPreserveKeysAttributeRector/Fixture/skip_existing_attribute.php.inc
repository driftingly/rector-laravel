<?php

namespace RectorLaravel\Tests\Rector\Class_\PreserveKeysPropertyToPreserveKeysAttributeRector\Fixture;

use Illuminate\Http\Resources\Attributes\PreserveKeys;
use Illuminate\Http\Resources\Json\JsonResource;

#[PreserveKeys]
class UserResourceWithExistingAttribute extends JsonResource
{
    protected $preserveKeys = true;
}
