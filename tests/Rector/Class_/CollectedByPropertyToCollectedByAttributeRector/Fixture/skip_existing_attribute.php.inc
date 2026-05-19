<?php

namespace RectorLaravel\Tests\Rector\Class_\CollectedByPropertyToCollectedByAttributeRector\Fixture;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

#[\Illuminate\Database\Eloquent\Attributes\CollectedBy(\Illuminate\Database\Eloquent\Collection::class)]
class SkipExistingAttribute extends Model
{
    /**
     * @param array<int, \Illuminate\Database\Eloquent\Model> $models
     * @return \Illuminate\Database\Eloquent\Collection<int, \Illuminate\Database\Eloquent\Model>
     */
    public function newCollection(array $models = []): Collection
    {
        return new \Illuminate\Database\Eloquent\Collection($models);
    }
}

?>
