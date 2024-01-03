<?php

namespace RectorLaravel\Tests\Rector\MethodCall\JsonCallToExplicitJsonCallRector\Fixture;

class FixtureWithJsonCalls
{
    public function testHead(\Illuminate\Foundation\Testing\Concerns\MakesHttpRequests $http)
    {
        $http->json('head', '/');
    }

    public function testTrace(\Illuminate\Foundation\Testing\Concerns\MakesHttpRequests $http)
    {
        $http->json('trace', '/');
    }

    public function testConnect(\Illuminate\Foundation\Testing\Concerns\MakesHttpRequests $http)
    {
        $http->json('connect', '/');
    }
}

?>
---
<?php

namespace RectorLaravel\Tests\Rector\MethodCall\AssertStatusToAssertMethodRector\Fixture;

class FixtureWithJsonCalls
{
    public function testHead(\Illuminate\Foundation\Testing\Concerns\MakesHttpRequests $http)
    {
        $http->json('head', '/');
    }

    public function testTrace(\Illuminate\Foundation\Testing\Concerns\MakesHttpRequests $http)
    {
        $http->json('trace', '/');
    }

    public function testConnect(\Illuminate\Foundation\Testing\Concerns\MakesHttpRequests $http)
    {
        $http->json('connect', '/');
    }
}

?>

