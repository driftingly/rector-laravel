<?php

namespace RectorLaravel\Tests\Rector\Class_\CollectedByPropertyToCollectedByAttributeRector\Fixture;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class SkipStatementBeforeReturn extends Model
{
    public function newCollection(array $models = []): Collection
    {
        logger()->info('collecting');

        return new Collection($models);
    }
}

?>
