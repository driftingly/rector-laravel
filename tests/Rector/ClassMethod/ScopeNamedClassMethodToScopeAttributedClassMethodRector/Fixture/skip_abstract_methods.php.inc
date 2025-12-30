<?php

namespace RectorLaravel\Tests\Rector\ScopeNamedClassMethodToScopeAttributedClassMethodRector\Fixture;

use Illuminate\Database\Eloquent\Model;

class SkipAbstractMethods extends Model
{
    abstract public function scopeSomeMethod();
}
