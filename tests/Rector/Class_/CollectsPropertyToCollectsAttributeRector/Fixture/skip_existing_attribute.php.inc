<?php

namespace RectorLaravel\Tests\Rector\Class_\CollectsPropertyToCollectsAttributeRector\Fixture;

use Illuminate\Http\Resources\Attributes\Collects;
use Illuminate\Http\Resources\Json\JsonResource;

#[Collects(\RectorLaravel\Tests\Rector\Class_\CollectsPropertyToCollectsAttributeRector\Fixture\UserResource::class)]
class UserCollectionWithExistingAttribute extends JsonResource
{
    protected $collects = \RectorLaravel\Tests\Rector\Class_\CollectsPropertyToCollectsAttributeRector\Fixture\UserResource::class;
}
