<?php

declare(strict_types=1);

namespace RectorLaravel\Tests\Rector\ClassMethod\RelationTableStringToPivotClassRector\Source;

use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Model;

#[Table(name: 'post_tag')]
class PostTag extends Model {}
