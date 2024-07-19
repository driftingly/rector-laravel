<?php

namespace RectorLaravel\Tests\Rector\MethodCall\JsonCallToExplicitJsonCallRector\Fixture;

use Illuminate\Foundation\Testing\Concerns\MakesHttpRequests;

class FixtureWithJsonCalls
{
    public function testHead(MakesHttpRequests $http)
    {
        $http->json('head', '/');
    }

    public function testTrace(MakesHttpRequests $http)
    {
        $http->json('trace', '/');
    }

    public function testConnect(MakesHttpRequests $http)
    {
        $http->json('connect', '/');
    }

    public function testNotEnoughArgs(MakesHttpRequests $http)
    {
        $http->json('GET');
        $http->json();
    }
}

?>
