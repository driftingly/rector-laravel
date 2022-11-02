<?php

namespace RectorLaravel\Tests\Rector\StaticCall\MinutesToSecondsInCacheRector\Fixture;

class CacheStaticCall
{
    public function run()
    {
        \Illuminate\Support\Facades\Cache::remember('key', 'value', 60);
    }
}

?>
-----
<?php

namespace RectorLaravel\Tests\Rector\StaticCall\MinutesToSecondsInCacheRector\Fixture;

class CacheStaticCall
{
    public function run()
    {
        \Illuminate\Support\Facades\Cache::remember('key', 'value', 60 * 60);
    }
}

?>
