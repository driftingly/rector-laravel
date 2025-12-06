<?php

namespace RectorLaravel\Tests\NodeAnalyzer\Source;

use Illuminate\Database\Eloquent\Model;

class SomeModelWithCustomTableAndPrimaryKey extends Model
{
    protected $table = 'custom_table';

    protected $primaryKey = 'uuid';
}
