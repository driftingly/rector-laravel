<?php

namespace RectorLaravel\Tests\Rector\Class_\PreserveKeysPropertyToPreserveKeysAttributeRector\Fixture;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResourcePreserveKeysString extends JsonResource
{
    protected $preserveKeys = 'true';
}
