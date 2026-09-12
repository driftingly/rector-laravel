<?php

declare(strict_types=1);

namespace RectorLaravel\Tests\Rector\ClassMethod\RelationTableStringToPivotClassRector\Source;

use Illuminate\Database\Eloquent\Model;

class ArticleLabel extends Model
{
    protected $table = 'article_label';
}
