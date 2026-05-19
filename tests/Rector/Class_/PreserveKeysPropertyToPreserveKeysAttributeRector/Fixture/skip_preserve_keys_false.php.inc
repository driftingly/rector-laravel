<?php

namespace RectorLaravel\Tests\Rector\Class_\PreserveKeysPropertyToPreserveKeysAttributeRector\Fixture;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResourcePreserveKeysFalse extends JsonResource
{
    protected $preserveKeys = false;
}
