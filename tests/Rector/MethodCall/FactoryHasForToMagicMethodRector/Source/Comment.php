<?php

namespace RectorLaravel\Tests\Rector\MethodCall\FactoryHasForToMagicMethodRector\Source;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use HasFactory;
}
