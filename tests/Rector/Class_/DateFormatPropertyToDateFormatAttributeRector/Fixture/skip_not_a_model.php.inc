<?php

namespace RectorLaravel\Tests\Rector\Class_\DateFormatPropertyToDateFormatAttributeRector\Fixture;

use Illuminate\Database\Eloquent\Model;

class SkipNotAModel
{
    protected $dateFormat = 'U';
}

?>
