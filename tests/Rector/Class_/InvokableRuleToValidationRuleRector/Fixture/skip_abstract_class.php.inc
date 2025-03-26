<?php

namespace RectorLaravel\Tests\Rector\Class_\InvokableRuleToValidationRuleRector\Fixture;

abstract class SkipAbstractClass implements \Illuminate\Contracts\Validation\InvokableRule
{
    public function __invoke(string $attribute, mixed $value, callable $fail)
    {
    }
}

?>
