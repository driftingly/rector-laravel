<?php

namespace RectorLaravel\Tests\Rector\Class_\PreserveKeysPropertyToPreserveKeysAttributeRector\Fixture;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    protected $preserveKeys = true;
}

?>
-----
<?php

namespace RectorLaravel\Tests\Rector\Class_\PreserveKeysPropertyToPreserveKeysAttributeRector\Fixture;

use Illuminate\Http\Resources\Json\JsonResource;

#[\Illuminate\Http\Resources\Attributes\PreserveKeys]
class UserResource extends JsonResource
{
}

?>
