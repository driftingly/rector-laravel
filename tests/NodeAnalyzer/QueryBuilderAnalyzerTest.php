<?php

namespace NodeAnalyzer;

use PhpParser\Node\Arg;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\New_;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Name\FullyQualified;
use PhpParser\Node\Scalar\String_;
use PHPUnit\Framework\Assert;
use Rector\Testing\PHPUnit\AbstractLazyTestCase;
use RectorLaravel\NodeAnalyzer\QueryBuilderAnalyzer;

class QueryBuilderAnalyzerTest extends AbstractLazyTestCase
{
    /**
     * @test
     */
    public function it_finds_if_the_static_call_is_proxying_a_query_builder(): void
    {
        $queryBuilderAnalyzer = $this->make(QueryBuilderAnalyzer::class);

        Assert::assertTrue($queryBuilderAnalyzer->isProxyCall(new StaticCall(
            new FullyQualified('UserLand\SomeModel'),
            'where',
            [new Arg(new String_('id')), new Arg(new String_('1'))]
        )));
    }

    /**
     * @test
     */
    public function it_finds_if_the_static_call_is_not_proxying_a_query_builder(): void
    {
        $queryBuilderAnalyzer = $this->make(QueryBuilderAnalyzer::class);

        Assert::assertFalse($queryBuilderAnalyzer->isProxyCall(new StaticCall(
            new FullyQualified('UserLand\SomeService'),
            'where',
            [new Arg(new String_('id')), new Arg(new String_('1'))]
        )));
    }

    /**
     * @test
     */
    public function it_detects_if_matches_static_call_on_a_model(): void
    {
        $queryBuilderAnalyzer = $this->make(QueryBuilderAnalyzer::class);

        Assert::assertTrue($queryBuilderAnalyzer->isMatchingCall(new StaticCall(
            new FullyQualified('UserLand\SomeModel'),
            'where',
            [new Arg(new String_('id')), new Arg(new String_('1'))]
        ), 'where'));
    }

    /**
     * @test
     */
    public function it_detects_if_matches_method_call_on_a_query_builder(): void
    {
        $queryBuilderAnalyzer = $this->make(QueryBuilderAnalyzer::class);

        Assert::assertTrue($queryBuilderAnalyzer->isMatchingCall(new MethodCall(
            new New_(
                new FullyQualified('Illuminate\Contracts\Database\Query\Builder'),
                []
            ),
            'where',
            [new Arg(new String_('id')), new Arg(new String_('1'))]
        ), 'where'));
    }
}
