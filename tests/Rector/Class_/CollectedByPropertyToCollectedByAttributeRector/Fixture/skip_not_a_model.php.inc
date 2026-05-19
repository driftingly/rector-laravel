<?php

namespace RectorLaravel\Tests\Rector\Class_\CollectedByPropertyToCollectedByAttributeRector\Fixture;

use Illuminate\Database\Eloquent\Model;

class SkipNotAModel
{
    public function newCollection(array $models = []): \Illuminate\Database\Eloquent\Collection
    {
        return new \Illuminate\Database\Eloquent\Collection($models);
    }
}

?>
