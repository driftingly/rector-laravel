<?php

namespace RectorLaravel\Tests\Rector\MethodCall\JsonCallToExplicitJsonCallRector\Fixture;

class FixtureWithJsonCalls
{
    public function testGet(\Illuminate\Foundation\Testing\Concerns\MakesHttpRequests $http)
    {
        $http->json('get', '/');
    }

    public function testPost(\Illuminate\Foundation\Testing\Concerns\MakesHttpRequests $http)
    {
        $http->json('post', '/');
    }

    public function testPut(\Illuminate\Foundation\Testing\Concerns\MakesHttpRequests $http)
    {
        $http->json('put', '/');
    }

    public function testPatch(\Illuminate\Foundation\Testing\Concerns\MakesHttpRequests $http)
    {
        $http->json('patch', '/');
    }

    public function testDelete(\Illuminate\Foundation\Testing\Concerns\MakesHttpRequests $http)
    {
        $http->json('delete', '/');
    }

    public function testOptions(\Illuminate\Foundation\Testing\Concerns\MakesHttpRequests $http)
    {
        $http->json('options', '/');
    }
}

?>
---
<?php

namespace RectorLaravel\Tests\Rector\MethodCall\AssertStatusToAssertMethodRector\Fixture;

class FixtureWithJsonCalls
{
    public function testGet(\Illuminate\Foundation\Testing\Concerns\MakesHttpRequests $http)
    {
        $http->getJson('/');
    }

    public function testPost(\Illuminate\Foundation\Testing\Concerns\MakesHttpRequests $http)
    {
        $http->postJson('/');
    }

    public function testPut(\Illuminate\Foundation\Testing\Concerns\MakesHttpRequests $http)
    {
        $http->putJson('/');
    }

    public function testPatch(\Illuminate\Foundation\Testing\Concerns\MakesHttpRequests $http)
    {
        $http->patchJson('/');
    }

    public function testDelete(\Illuminate\Foundation\Testing\Concerns\MakesHttpRequests $http)
    {
        $http->deleteJson('/');
    }

    public function testOptions(\Illuminate\Foundation\Testing\Concerns\MakesHttpRequests $http)
    {
        $http->optionsJson('/');
    }
}

?>

