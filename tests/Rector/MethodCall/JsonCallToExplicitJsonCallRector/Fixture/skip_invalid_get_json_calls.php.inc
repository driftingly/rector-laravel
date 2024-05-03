<?php

namespace RectorLaravel\Tests\Rector\MethodCall\JsonCallToExplicitJsonCallRector\Fixture;

class FixtureWithGetJsonCalls
{
    public function testGetWithAllParams(\Illuminate\Foundation\Testing\Concerns\MakesHttpRequests $http)
    {
        $http->json('GET', '/', ['data'], ['headers'], 1);
    }

    public function testGetWithoutOptions(\Illuminate\Foundation\Testing\Concerns\MakesHttpRequests $http)
    {
        $http->json('GET', '/', ['data'], ['headers']);
    }

    public function testGetWithoutHeaders(\Illuminate\Foundation\Testing\Concerns\MakesHttpRequests $http)
    {
        $http->json('GET', '/', ['data']);
    }
}

?>
