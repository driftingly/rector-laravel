<?php

namespace RectorLaravel\Tests\Rector\MethodCall\DateWhereClauseToShorthandRector\Fixture;

use Illuminate\Database\Eloquent\Model;

final class SkipParentNativeStaticCall extends Model
{
    protected function casts(): array
    {
        return [
            ...parent::casts(),
        ];
    }
}
