<?php

namespace RectorLaravel\Tests\Rector\Class_\CollectsPropertyToCollectsAttributeRector\Fixture;

use Illuminate\Http\Resources\Json\JsonResource;

class UserCollection extends JsonResource
{
    protected $collects = \RectorLaravel\Tests\Rector\Class_\CollectsPropertyToCollectsAttributeRector\Fixture\UserResource::class;
}

?>
-----
<?php

namespace RectorLaravel\Tests\Rector\Class_\CollectsPropertyToCollectsAttributeRector\Fixture;

use Illuminate\Http\Resources\Json\JsonResource;

#[\Illuminate\Http\Resources\Attributes\Collects(\RectorLaravel\Tests\Rector\Class_\CollectsPropertyToCollectsAttributeRector\Fixture\UserResource::class)]
class UserCollection extends JsonResource
{
}

?>
