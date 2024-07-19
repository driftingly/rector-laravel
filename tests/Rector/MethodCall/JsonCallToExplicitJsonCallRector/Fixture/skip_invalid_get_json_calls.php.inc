<?php

namespace RectorLaravel\Tests\Rector\MethodCall\JsonCallToExplicitJsonCallRector\Fixture;

use Illuminate\Foundation\Testing\Concerns\MakesHttpRequests;

class FixtureWithGetJsonCalls
{
    public function testGetWithAllParams(MakesHttpRequests $http)
    {
        $http->json('GET', '/', ['data'], ['headers'], 1);
    }

    public function testGetWithoutOptions(MakesHttpRequests $http)
    {
        $http->json('GET', '/', ['data'], ['headers']);
    }

    public function testGetWithoutHeaders(MakesHttpRequests $http)
    {
        $http->json('GET', '/', ['data']);
    }

    public function testGetWithNamedHeaders(MakesHttpRequests $http)
    {
        $http->json('GET', route('home'), headers: ['payload']);
    }

    public function testGetWithNamedOptions(MakesHttpRequests $http)
    {
        $http->json('GET', route('home'), options: 1);
    }
}

?>
