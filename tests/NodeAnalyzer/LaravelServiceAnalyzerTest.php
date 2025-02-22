<?php

namespace NodeAnalyzer;

use PhpParser\Node\Arg;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\New_;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Name;
use PhpParser\Node\Name\FullyQualified;
use PhpParser\Node\Scalar\String_;
use PHPStan\Type\ObjectType;
use PHPUnit\Framework\Assert;
use Rector\Testing\PHPUnit\AbstractLazyTestCase;
use RectorLaravel\NodeAnalyzer\LaravelServiceAnalyzer;

class LaravelServiceAnalyzerTest extends AbstractLazyTestCase
{
    /**
     * @test
     */
    public function it_can_find_the_proxy_origin_of_the_facade_with_string_accessor(): void
    {
        $laravelServiceAnalyzer = $this->make(LaravelServiceAnalyzer::class);

        $staticCall = new StaticCall(
            new Name('Illuminate\Support\Facades\DB'),
            'table',
            [new Arg(new String_('table'))]
        );

        $objectType = $laravelServiceAnalyzer->getFacadeOrigin($staticCall);

        Assert::assertInstanceOf(ObjectType::class, $objectType);
        Assert::assertSame('Illuminate\Database\DatabaseManager', $objectType->getClassName());
    }

    /**
     * @test
     */
    public function it_can_find_the_proxy_origin_of_the_facade_with_direct_class_accessor(): void
    {
        $laravelServiceAnalyzer = $this->make(LaravelServiceAnalyzer::class);

        $staticCall = new StaticCall(
            new Name('UserLand\SomeFacade'),
            'someCall',
            []
        );

        $objectType = $laravelServiceAnalyzer->getFacadeOrigin($staticCall);

        Assert::assertInstanceOf(ObjectType::class, $objectType);
        Assert::assertSame('UserLand\SomeService', $objectType->getClassName());
    }

    /**
     * @test
     */
    public function it_can_detect_the_static_call_is_made_via_facade(): void
    {
        $laravelServiceAnalyzer = $this->make(LaravelServiceAnalyzer::class);

        $staticCall = new StaticCall(
            new Name('Illuminate\Support\Facades\DB'),
            'table',
            [new Arg(new String_('table'))]
        );

        Assert::assertTrue($laravelServiceAnalyzer->isFacadeCall($staticCall));
    }

    /**
     * @test
     */
    public function it_can_detect_the_static_call_is_not_made_via_facade(): void
    {
        $laravelServiceAnalyzer = $this->make(LaravelServiceAnalyzer::class);

        $staticCall = new StaticCall(
            new Name('SomeClass'),
            'table',
            [new Arg(new String_('table'))]
        );

        Assert::assertFalse($laravelServiceAnalyzer->isFacadeCall($staticCall));
    }

    /**
     * @test
     */
    public function it_can_match_facade_calls_to_the_underlying_service(): void
    {
        $laravelServiceAnalyzer = $this->make(LaravelServiceAnalyzer::class);

        $staticCall = new StaticCall(
            new Name('Illuminate\Support\Facades\DB'),
            'table',
            [new Arg(new String_('table'))]
        );

        Assert::assertTrue($laravelServiceAnalyzer->isMatchingCall(
            $staticCall,
            new ObjectType('Illuminate\Database\DatabaseManager'),
            'table',
        ));
    }

    /**
     * @test
     */
    public function it_can_match_direct_calls_to_the_service_by_method_call(): void
    {
        $laravelServiceAnalyzer = $this->make(LaravelServiceAnalyzer::class);

        $methodCall = new MethodCall(
            new New_(
                new FullyQualified('Illuminate\Database\DatabaseManager'),
                []
            ),
            'table',
            [new Arg(new String_('table'))]
        );

        Assert::assertTrue($laravelServiceAnalyzer->isMatchingCall(
            $methodCall,
            new ObjectType('Illuminate\Database\DatabaseManager'),
            'table',
        ));
    }

    /**
     * @test
     */
    public function it_can_does_not_match_static_calls(): void
    {
        $laravelServiceAnalyzer = $this->make(LaravelServiceAnalyzer::class);

        $staticCall = new StaticCall(
            new Name('Illuminate\Database\DatabaseManager'),
            'table',
            [new Arg(new String_('table'))]
        );

        Assert::assertFalse($laravelServiceAnalyzer->isMatchingCall(
            $staticCall,
            new ObjectType('Illuminate\Database\DatabaseManager'),
            'table',
        ));
    }
}
