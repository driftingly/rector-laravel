<?php

namespace RectorLaravel\Tests\Rector\Class_\CollectedByPropertyToCollectedByAttributeRector\Fixture;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class UserCollection extends Collection {}

class SomeOtherCollection extends Collection {}

#[\Illuminate\Database\Eloquent\Attributes\CollectedBy(SomeOtherCollection::class)]
class SkipExistingAttributeWithDifferentValue extends Model
{
    /**
     * @param array<int, \Illuminate\Database\Eloquent\Model> $models
     * @return \Illuminate\Database\Eloquent\Collection<int, \Illuminate\Database\Eloquent\Model>
     */
    public function newCollection(array $models = []): Collection
    {
        return new UserCollection($models);
    }
}

?>
