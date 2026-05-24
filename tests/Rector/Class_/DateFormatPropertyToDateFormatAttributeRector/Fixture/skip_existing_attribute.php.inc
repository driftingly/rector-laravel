<?php

namespace RectorLaravel\Tests\Rector\Class_\DateFormatPropertyToDateFormatAttributeRector\Fixture;

use Illuminate\Database\Eloquent\Model;

#[\Illuminate\Database\Eloquent\Attributes\DateFormat('U')]
class SkipExistingAttribute extends Model
{
    protected $dateFormat = 'U';
}

?>
