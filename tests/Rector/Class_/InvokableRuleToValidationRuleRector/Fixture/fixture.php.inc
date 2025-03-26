<?php

namespace RectorLaravel\Tests\Rector\Class_\InvokableRuleToValidationRuleRector\Fixture;

class FooRule implements \Illuminate\Contracts\Validation\InvokableRule
{
    public function __invoke(string $attribute, mixed $value, callable $fail)
    {
    }
}

?>
-----
<?php

namespace RectorLaravel\Tests\Rector\Class_\InvokableRuleToValidationRuleRector\Fixture;

class FooRule implements \Illuminate\Contracts\Validation\ValidationRule
{
    public function validate(string $attribute, mixed $value, callable $fail)
    {
    }
}

?>
