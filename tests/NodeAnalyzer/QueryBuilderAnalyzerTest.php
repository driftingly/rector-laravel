<?php

namespace RectorLaravel\Tests\NodeAnalyzer;

use Exception;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\New_;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Name\FullyQualified;
use PhpParser\Node\Scalar\String_;
use PhpParser\Node\Stmt\Expression;
use PhpParser\Node\Stmt\Namespace_;
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

    /**
     * @test
     */
    public function if_resolves_the_model_on_eloquent_queries(): void
    {
        $rectorParser = $this->make(RectorParser::class);
        $queryBuilderAnalyzer = $this->make(QueryBuilderAnalyzer::class);
        $nodeTypeResolver = $this->make(NodeTypeResolver::class);
        $nodeScopeAndMetadataDecorator = $this->make(NodeScopeAndMetadataDecorator::class);

        /** @var Namespace_[] $statements */
        $statements = $rectorParser->parseFile(__DIR__ . '/fixtures/query-resolved.php');
        /** @var Namespace_[] $statements */
        $statements = $nodeScopeAndMetadataDecorator->decorateNodesFromFile(
            __DIR__ . '/fixtures/query-resolved.php',
            $statements
        );

        if (
            ! $statements[0]->stmts[2] instanceof Expression ||
            ! $statements[0]->stmts[2]->expr instanceof MethodCall ||
            ! $statements[0]->stmts[2]->expr->var instanceof Variable
        ) {
            throw new Exception('Fixture nodes are incorrect');
        }

        $variable = $statements[0]->stmts[2]->expr->var;

        $scope = ScopeFetcher::fetch($variable);
        $initialType = $nodeTypeResolver->getType($variable);
        $foundType = $queryBuilderAnalyzer->resolveQueryBuilderModel($initialType, $scope);

        Assert::assertNotNull($foundType);

        Assert::assertTrue(
            $foundType->isSuperTypeOf(
                new ObjectType('RectorLaravel\Tests\NodeAnalyzer\Source\Foo')
            )->yes()
        );
        Assert::assertFalse(
            $foundType->isSuperTypeOf(
                new ObjectType('RectorLaravel\Tests\NodeAnalyzer\Source\Bar')
            )->yes()
        );
    }

    /**
     * @test
     */
    public function it_analyses_if_call_node_is_using_a_query_builder_with_specific_model(): void
    {
        $rectorParser = $this->make(RectorParser::class);
        $queryBuilderAnalyzer = $this->make(QueryBuilderAnalyzer::class);
        $nodeScopeAndMetadataDecorator = $this->make(NodeScopeAndMetadataDecorator::class);

        /** @var Namespace_[] $statements */
        $statements = $rectorParser->parseFile(__DIR__ . '/fixtures/query-resolved.php');
        /** @var Namespace_[] $statements */
        $statements = $nodeScopeAndMetadataDecorator->decorateNodesFromFile(
            __DIR__ . '/fixtures/query-resolved.php',
            $statements
        );

        if (
            ! $statements[0]->stmts[2] instanceof Expression ||
            ! $statements[0]->stmts[2]->expr instanceof MethodCall ||
            ! $statements[0]->stmts[2]->expr->var instanceof Variable
        ) {
            throw new Exception('Fixture nodes are incorrect');
        }

        $result = $queryBuilderAnalyzer->isQueryUsingModel(
            $statements[0]->stmts[2]->expr->var,
            new ObjectType('RectorLaravel\Tests\NodeAnalyzer\Source\Foo')
        );

        Assert::assertTrue($result);

        $result = $queryBuilderAnalyzer->isQueryUsingModel(
            $statements[0]->stmts[2]->expr->var,
            new ObjectType('RectorLaravel\Tests\NodeAnalyzer\Source\Bar')
        );

        Assert::assertFalse($result);
    }
}
