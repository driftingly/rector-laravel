<?php

namespace RectorLaravel\Tests\Rector\Class_\CollectedByPropertyToCollectedByAttributeRector\Fixture;

use Illuminate\Database\Eloquent\Model;

#[\Illuminate\Database\Eloquent\Attributes\CollectedBy(\Illuminate\Database\Eloquent\Collection::class)]
class SkipExistingAttribute extends Model
{
    protected $collectionClass = \Illuminate\Database\Eloquent\Collection::class;
}

?>
