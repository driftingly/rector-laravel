<?php

namespace RectorLaravel\Tests\Rector\ClassMethod\MakeModelAttributesAndScopesProtectedRector\Fixture;

use Illuminate\Database\Eloquent\Model;

class SkipScopeMethodNotScope extends Model
{
    public function scope(): string
    {
        return 'foo';
    }
}

?>
