<?php

namespace RectorLaravel\Tests\Rector\FuncCall\ConfigToTypedConfigMethodCallRector\Fixture;

class ComplexConfigCasts
{
    public function run()
    {
        // Multiple casts in one method
        $appName = (string) config('app.name');
        $appDebug = (bool) config('app.debug');

        // Nested in expressions
        $url = 'https://' . (string) config('app.domain');

        // In conditionals
        if ((bool) config('feature.enabled')) {
            return true;
        }

        // In return statements
        return (int) config('cache.ttl');
    }
}

?>
-----
<?php

namespace RectorLaravel\Tests\Rector\FuncCall\ConfigToTypedConfigMethodCallRector\Fixture;

class ComplexConfigCasts
{
    public function run()
    {
        // Multiple casts in one method
        $appName = config()->string('app.name');
        $appDebug = config()->boolean('app.debug');

        // Nested in expressions
        $url = 'https://' . config()->string('app.domain');

        // In conditionals
        if (config()->boolean('feature.enabled')) {
            return true;
        }

        // In return statements
        return config()->integer('cache.ttl');
    }
}

?>
