<?php

namespace RectorLaravel\Tests\Rector\ClassMethod\RelationTableStringToPivotClassRector\Source;

use Illuminate\Database\Eloquent\Model;
use RectorLaravel\Tests\Rector\ClassMethod\RelationTableStringToPivotClassRector\Source\External\Label;

class Article extends Model
{
    public function labels()
    {
        return $this->belongsToMany(Label::class, 'article_label');
    }
}

?>
-----
<?php

namespace RectorLaravel\Tests\Rector\ClassMethod\RelationTableStringToPivotClassRector\Source;

use Illuminate\Database\Eloquent\Model;
use RectorLaravel\Tests\Rector\ClassMethod\RelationTableStringToPivotClassRector\Source\External\Label;

class Article extends Model
{
    public function labels()
    {
        return $this->belongsToMany(Label::class, \RectorLaravel\Tests\Rector\ClassMethod\RelationTableStringToPivotClassRector\Source\ArticleLabel::class);
    }
}

?>
