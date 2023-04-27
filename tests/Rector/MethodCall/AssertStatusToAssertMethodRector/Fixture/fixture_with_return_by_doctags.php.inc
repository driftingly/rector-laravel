<?php

namespace RectorLaravel\Tests\Rector\MethodCall\AssertStatusToAssertMethodRector\Fixture;

class FixtureWithReturnByDoctagsTest extends TestCase
{
    public function testOk()
    {
        $this->get('/')->assertStatus(\Symfony\Component\HttpFoundation\Response::HTTP_OK);
    }
}

class TestCase
{
    /**
     * @return \Illuminate\Testing\TestResponse
     */
    public function get($path)
    {

    }
}

?>
-----
<?php

namespace RectorLaravel\Tests\Rector\MethodCall\AssertStatusToAssertMethodRector\Fixture;

class FixtureWithReturnByDoctagsTest extends TestCase
{
    public function testOk()
    {
        $this->get('/')->assertOk();
    }
}

class TestCase
{
    /**
     * @return \Illuminate\Testing\TestResponse
     */
    public function get($path)
    {

    }
}

?>
