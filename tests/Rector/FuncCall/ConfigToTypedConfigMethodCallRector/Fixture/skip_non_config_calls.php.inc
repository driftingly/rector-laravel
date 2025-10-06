<?php

namespace RectorLaravel\Tests\Rector\FuncCall\ConfigToTypedConfigMethodCallRector\Fixture;

class SkipNonConfigCalls
{
    public function run()
    {
        // Should skip: config() without arguments
        $config = config();

        // Should skip: config() with array argument (setting values)
        config(['app.name' => 'My App']);

        // Should skip: config() without cast
        $value = config('app.name');

        // Should skip: other function casts
        $other = (string) someOtherFunc('key');
    }
}
