<?php

namespace NodeAnalyzer;

use PhpParser\Node\Arg;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Name;
use PhpParser\Node\Scalar\String_;
use PhpParser\Node\Stmt\Expression;
use Rector\NodeTypeResolver\PHPStan\Scope\PHPStanNodeScopeResolver;
use Rector\Testing\PHPUnit\AbstractLazyTestCase;
use RectorLaravel\NodeAnalyzer\LaravelServiceAnalyzer;
use PHPStan\Analyser\MutatingScope;
use PHPStan\Type\ObjectType;

class LaravelServiceAnalyzerTest extends AbstractLazyTestCase
{
    public function testItCanFindTheProxyOriginOfTheFacadeWithStringAccessor(): void
    {
        $analyzer = $this->make(LaravelServiceAnalyzer::class);

        $facadeCall = new StaticCall(
            new Name('Illuminate\Support\Facades\DB'),
            'table',
            [new Arg(new String_('table'))]
        );

        $objectType = $analyzer->getFacadeOrigin($facadeCall);

        $this->assertInstanceOf(ObjectType::class, $objectType);
        $this->assertSame('Illuminate\Database\DatabaseManager', $objectType->getClassName());
    }

    public function testItCanFindTheProxyOriginOfTheFacadeWithDirectClassAccessor(): void
    {
        $analyzer = $this->make(LaravelServiceAnalyzer::class);

        $facadeCall = new StaticCall(
            new Name('UserLand\SomeFacade'),
            'someCall',
            []
        );

        $objectType = $analyzer->getFacadeOrigin($facadeCall);

        $this->assertInstanceOf(ObjectType::class, $objectType);
        $this->assertSame('UserLand\SomeService', $objectType->getClassName());
    }

    public function testItCanDetectTheStaticCallIsMadeViaFacade(): void
    {
        $analyzer = $this->make(LaravelServiceAnalyzer::class);

        $facadeCall = new StaticCall(
            new Name('Illuminate\Support\Facades\DB'),
            'table',
            [new Arg(new String_('table'))]
        );

        $this->assertTrue($analyzer->isFacadeCall($facadeCall));
    }

    public function testItCanDetectTheStaticCallIsNotMadeViaFacade(): void
    {
        $analyzer = $this->make(LaravelServiceAnalyzer::class);

        $facadeCall = new StaticCall(
            new Name('SomeClass'),
            'table',
            [new Arg(new String_('table'))]
        );

        $this->assertFalse($analyzer->isFacadeCall($facadeCall));
    }

    public function testItCanMatchFacadeCallsToTheUnderlyingService(): void
    {
        $analyzer = $this->make(LaravelServiceAnalyzer::class);

        $facadeCall = new StaticCall(
            new Name('Illuminate\Support\Facades\DB'),
            'table',
            [new Arg(new String_('table'))]
        );

        $this->assertTrue($analyzer->isMatchingCall(
            $facadeCall,
            new ObjectType('Illuminate\Database\DatabaseManager'),
            'table',
        ));
    }

    public function testItCanMatchDirectCallsToTheServiceByMethodCall(): void
    {
        $analyzer = $this->make(LaravelServiceAnalyzer::class);

        $methodCall = new MethodCall(
            new Variable('db'),
            'table',
            [new Arg(new String_('table'))]
        );

        $this->assertTrue($analyzer->isMatchingCall(
            $methodCall,
            new ObjectType('Illuminate\Database\DatabaseManager'),
            'table',
        ));
    }

    public function testItCanDoesNotMatchStaticCalls(): void
    {
        $analyzer = $this->make(LaravelServiceAnalyzer::class);

        $methodCall = new StaticCall(
            new Name('Illuminate\Database\DatabaseManager'),
            'table',
            [new Arg(new String_('table'))]
        );

        $this->assertFalse($analyzer->isMatchingCall(
            $methodCall,
            new ObjectType('Illuminate\Database\DatabaseManager'),
            'table',
        ));
    }
}
