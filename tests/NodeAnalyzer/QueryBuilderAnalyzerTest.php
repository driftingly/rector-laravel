<?php

namespace RectorLaravel\Tests\NodeAnalyzer;

use PhpParser\Node\Arg;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\New_;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Name\FullyQualified;
use PhpParser\Node\Scalar\String_;
use PHPStan\Type\ObjectType;
use PHPUnit\Framework\Assert;
use Rector\NodeTypeResolver\NodeScopeAndMetadataDecorator;
use Rector\NodeTypeResolver\NodeTypeResolver;
use Rector\PhpParser\Parser\RectorParser;
use Rector\PHPStan\ScopeFetcher;
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

    public function test_if_resolves_the_model_on_eloquent_queries(): void
    {
        $parser = $this->make(RectorParser::class);
        $queryBuilderAnalyzer = $this->make(QueryBuilderAnalyzer::class);
        $nodeTypeResolver = $this->make(NodeTypeResolver::class);
        $nodeScopeAndMetadataDecorator = $this->make(NodeScopeAndMetadataDecorator::class);

        $statements = $parser->parseFile(__DIR__ . '/fixtures/query-resolved.php');
        $statements = $nodeScopeAndMetadataDecorator->decorateNodesFromFile(
            __DIR__ . '/fixtures/query-resolved.php',
            $statements
        );

        $variable = $statements[0]->stmts[0]->expr->var;

        $scope = ScopeFetcher::fetch($variable);
        $initialType = $nodeTypeResolver->getType($variable);
        $foundType = $queryBuilderAnalyzer->resolveQueryBuilderModel($initialType, $scope);

        $this->assertNotNull($foundType);

        $this->assertTrue(
            $foundType->isSuperTypeOf(
                new ObjectType('RectorLaravel\Tests\NodeAnalyzer\Source\Foo')
            )->yes()
        );
        $this->assertFalse(
            $foundType->isSuperTypeOf(
                new ObjectType('RectorLaravel\Tests\NodeAnalyzer\Source\Bar')
            )->yes()
        );
    }

    public function test_it_analyses_if_call_node_is_using_a_query_builder_with_specific_model(): void
    {
        $parser = $this->make(RectorParser::class);
        $queryBuilderAnalyzer = $this->make(QueryBuilderAnalyzer::class);
        $nodeScopeAndMetadataDecorator = $this->make(NodeScopeAndMetadataDecorator::class);

        $statements = $parser->parseFile(__DIR__ . '/fixtures/query-resolved.php');
        $statements = $nodeScopeAndMetadataDecorator->decorateNodesFromFile(
            __DIR__ . '/fixtures/query-resolved.php',
            $statements
        );

        $result = $queryBuilderAnalyzer->isQueryUsingModel(
            $statements[0]->stmts[0]->expr->var,
            new ObjectType('RectorLaravel\Tests\NodeAnalyzer\Source\Foo')
        );

        $this->assertTrue($result);

        $result = $queryBuilderAnalyzer->isQueryUsingModel(
            $statements[0]->stmts[0]->expr->var,
            new ObjectType('RectorLaravel\Tests\NodeAnalyzer\Source\Bar')
        );

        $this->assertFalse($result);
    }
}
