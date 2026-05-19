<?php

namespace RectorLaravel\Tests\Rector\Class_\CollectedByPropertyToCollectedByAttributeRector\Fixture;

use Illuminate\Database\Eloquent\Model;

class SkipNotAModel
{
    protected $collectionClass = \Illuminate\Database\Eloquent\Collection::class;
}

?>
