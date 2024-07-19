<?php

namespace RectorLaravel\Tests\Rector\MethodCall\JsonCallToExplicitJsonCallRector\Fixture;

use Illuminate\Foundation\Testing\Concerns\MakesHttpRequests;

class FixtureWithJsonCalls
{
    public function testGet(MakesHttpRequests $http)
    {
        $http->json('get', '/');
    }

    public function testGetWithRouteHelper(MakesHttpRequests $http)
    {
        $http->json('GET', route('home'));
        $http->json('GET', route('home'), ['payload']);
        $http->json('GET', route('home'), data: ['payload']);
        $http->json('GET', route('home', 'some arg'), ['payload']);
    }

    public function testPost(MakesHttpRequests $http)
    {
        $http->json('post', '/');
    }

    public function testPut(MakesHttpRequests $http)
    {
        $http->json('put', '/');
    }

    public function testPatch(MakesHttpRequests $http)
    {
        $http->json('patch', '/');
    }

    public function testDelete(MakesHttpRequests $http)
    {
        $http->json('delete', '/');
    }

    public function testOptions(MakesHttpRequests $http)
    {
        $http->json('options', '/');
    }
}

?>
-----
<?php

namespace RectorLaravel\Tests\Rector\MethodCall\JsonCallToExplicitJsonCallRector\Fixture;

use Illuminate\Foundation\Testing\Concerns\MakesHttpRequests;

class FixtureWithJsonCalls
{
    public function testGet(MakesHttpRequests $http)
    {
        $http->getJson('/');
    }

    public function testGetWithRouteHelper(MakesHttpRequests $http)
    {
        $http->getJson(route('home'));
        $http->getJson(route('home', ['payload']));
        $http->getJson(route('home', ['payload']));
        $http->json('GET', route('home', 'some arg'), ['payload']);
    }

    public function testPost(MakesHttpRequests $http)
    {
        $http->postJson('/');
    }

    public function testPut(MakesHttpRequests $http)
    {
        $http->putJson('/');
    }

    public function testPatch(MakesHttpRequests $http)
    {
        $http->patchJson('/');
    }

    public function testDelete(MakesHttpRequests $http)
    {
        $http->deleteJson('/');
    }

    public function testOptions(MakesHttpRequests $http)
    {
        $http->optionsJson('/');
    }
}

?>
