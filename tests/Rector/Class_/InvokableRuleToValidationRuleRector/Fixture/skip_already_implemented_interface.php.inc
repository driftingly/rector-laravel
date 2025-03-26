<?php

namespace RectorLaravel\Tests\Rector\Class_\InvokableRuleToValidationRuleRector\Fixture;

class SkipAlreadyImplementedInterface  implements \Illuminate\Contracts\Validation\Invokable, \Illuminate\Contracts\Validation\ValidationRule
{
    public function __invoke(string $attribute, mixed $value, callable $fail)
    {
    }

    public function validate(string $attribute, mixed $value, callable $fail)
    {
    }
}

?>
