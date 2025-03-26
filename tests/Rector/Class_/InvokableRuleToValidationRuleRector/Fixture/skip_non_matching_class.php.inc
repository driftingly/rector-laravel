<?php

namespace RectorLaravel\Tests\Rector\Class_\InvokableRuleToValidationRuleRector\Fixture;

class SkipNonMatchingClass
{
    public function __invoke(string $attribute, mixed $value, callable $fail)
    {
    }
}

?>
