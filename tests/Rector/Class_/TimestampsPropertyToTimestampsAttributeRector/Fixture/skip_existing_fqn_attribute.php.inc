<?php

namespace RectorLaravel\Tests\Rector\Class_\TimestampsPropertyToTimestampsAttributeRector\Fixture;

use Illuminate\Database\Eloquent\Attributes\WithoutTimestamps;
use Illuminate\Database\Eloquent\Model;

#[WithoutTimestamps]
class ExistingFqnAttribute extends Model
{
}

?>
