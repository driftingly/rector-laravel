<?php

namespace NodeAnalyzer;

use Exception;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Stmt\Expression;
use PhpParser\Node\Stmt\Namespace_;
use PHPStan\Type\ObjectType;
use PHPUnit\Framework\Assert;
use Rector\NodeTypeResolver\NodeScopeAndMetadataDecorator;
use Rector\NodeTypeResolver\NodeTypeResolver;
use Rector\PhpParser\Parser\RectorParser;
use Rector\PHPStan\ScopeFetcher;
use Rector\Testing\PHPUnit\AbstractLazyTestCase;
use RectorLaravel\NodeAnalyzer\RelationshipAnalyzer;

class RelationshipAnalyzerTest extends AbstractLazyTestCase
{
    /**
     * @test
     */
    public function it_resolves_the_relative_model_on_relations(): void
    {
        $rectorParser = $this->make(RectorParser::class);
        $relationshipAnalyzer = $this->make(RelationshipAnalyzer::class);
        $nodeTypeResolver = $this->make(NodeTypeResolver::class);
        $nodeScopeAndMetadataDecorator = $this->make(NodeScopeAndMetadataDecorator::class);

        /** @var Namespace_[] $statements */
        $statements = $rectorParser->parseFile(__DIR__ . '/fixtures/relation-resolved.php');
        /** @var Namespace_[] $statements */
        $statements = $nodeScopeAndMetadataDecorator->decorateNodesFromFile(
            __DIR__ . '/fixtures/relation-resolved.php',
            $statements
        );

        if (
            ! $statements[0]->stmts[3] instanceof Expression ||
            ! $statements[0]->stmts[3]->expr instanceof MethodCall ||
            ! $statements[0]->stmts[3]->expr->var instanceof Variable
        ) {
            throw new Exception('Fixture nodes are incorrect');
        }

        $variable = $statements[0]->stmts[3]->expr->var;

        ScopeFetcher::fetch($variable);
        $initialType = $nodeTypeResolver->getType($variable);
        $foundType = $relationshipAnalyzer->resolveRelatedForRelation($initialType);

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
    public function it_resolves_the_parent_model_on_relations(): void
    {
        $rectorParser = $this->make(RectorParser::class);
        $relationshipAnalyzer = $this->make(RelationshipAnalyzer::class);
        $nodeTypeResolver = $this->make(NodeTypeResolver::class);
        $nodeScopeAndMetadataDecorator = $this->make(NodeScopeAndMetadataDecorator::class);

        /** @var Namespace_[] $statements */
        $statements = $rectorParser->parseFile(__DIR__ . '/fixtures/relation-resolved.php');
        /** @var Namespace_[] $statements */
        $statements = $nodeScopeAndMetadataDecorator->decorateNodesFromFile(
            __DIR__ . '/fixtures/relation-resolved.php',
            $statements
        );

        if (
            ! $statements[0]->stmts[3] instanceof Expression ||
            ! $statements[0]->stmts[3]->expr instanceof MethodCall ||
            ! $statements[0]->stmts[3]->expr->var instanceof Variable
        ) {
            throw new Exception('Fixture nodes are incorrect');
        }

        $variable = $statements[0]->stmts[3]->expr->var;

        ScopeFetcher::fetch($variable);
        $initialType = $nodeTypeResolver->getType($variable);
        $foundType = $relationshipAnalyzer->resolveParentForRelation($initialType);

        Assert::assertNotNull($foundType);

        Assert::assertTrue(
            $foundType->isSuperTypeOf(
                new ObjectType('RectorLaravel\Tests\NodeAnalyzer\Source\SomeModel')
            )->yes()
        );
        Assert::assertFalse(
            $foundType->isSuperTypeOf(
                new ObjectType('RectorLaravel\Tests\NodeAnalyzer\Source\Bar')
            )->yes()
        );
    }
}
