<?php

declare(strict_types=1);

namespace RectorLaravel\Tests\Rector\MethodCall\RelationTableStringToPivotClassRector\Source;

use Illuminate\Database\Eloquent\Model;

class CommentUser extends Model
{
    protected $table = 'legacy_comment_user';
}
