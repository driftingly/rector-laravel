<?php

namespace RectorLaravel\Tests\NodeAnalyzer\Source\Pivots;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    public function conventionalPivot()
    {
        return $this->belongsToMany(Tag::class, 'post_tag');
    }

    public function unrelatedModelAsTable()
    {
        return $this->belongsToMany(Tag::class, 'users');
    }
}
