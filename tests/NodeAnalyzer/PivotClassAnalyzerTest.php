<?php

namespace RectorLaravel\Tests\NodeAnalyzer;

use Exception;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Scalar\String_;
use PhpParser\Node\Stmt\ClassLike;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Namespace_;
use PhpParser\Node\Stmt\Return_;
use PHPUnit\Framework\Assert;
use Rector\NodeTypeResolver\NodeScopeAndMetadataDecorator;
use Rector\PhpParser\Parser\RectorParser;
use Rector\Testing\PHPUnit\AbstractLazyTestCase;
use RectorLaravel\NodeAnalyzer\PivotClassAnalyzer;

final class PivotClassAnalyzerTest extends AbstractLazyTestCase
{
    /**
     * @test
     */
    public function it_matches_the_table_argument_of_a_relation(): void
    {
        $pivotClassAnalyzer = $this->make(PivotClassAnalyzer::class);

        $classMethod = $this->parseClassMethod('inferredPivot', 'SomeModelWithPivotRelations.php');

        $arg = $pivotClassAnalyzer->matchRelationTableArg($this->resolveRelationCall($classMethod));

        Assert::assertInstanceOf(Arg::class, $arg);
        Assert::assertInstanceOf(String_::class, $arg->value);
        Assert::assertSame('foo_bar', $arg->value->value);
    }

    /**
     * @test
     */
    public function it_matches_the_table_argument_of_a_relation_declared_in_a_trait(): void
    {
        $pivotClassAnalyzer = $this->make(PivotClassAnalyzer::class);

        $classMethod = $this->parseClassMethod('inferredPivot', 'HasPivotRelations.php');

        $arg = $pivotClassAnalyzer->matchRelationTableArg($this->resolveRelationCall($classMethod));

        Assert::assertInstanceOf(Arg::class, $arg);
        Assert::assertInstanceOf(String_::class, $arg->value);
        Assert::assertSame('foo_bar', $arg->value->value);
    }

    /**
     * @test
     */
    public function it_does_not_match_a_call_which_is_not_a_relation(): void
    {
        $pivotClassAnalyzer = $this->make(PivotClassAnalyzer::class);

        $classMethod = $this->parseClassMethod('usingPivot', 'SomeModelWithPivotRelations.php');

        Assert::assertNull($pivotClassAnalyzer->matchRelationTableArg($this->resolveReturnedCall($classMethod)));
    }

    /**
     * @test
     */
    public function it_resolves_a_pivot_model_from_the_related_model_namespace(): void
    {
        $pivotClassAnalyzer = $this->make(PivotClassAnalyzer::class);

        $classMethod = $this->parseClassMethod('inferredPivot', 'SomeModelWithPivotRelations.php');

        $result = $pivotClassAnalyzer->resolvePivotClass(
            $classMethod,
            $this->resolveRelationCall($classMethod),
            'foo_bar'
        );

        Assert::assertSame('RectorLaravel\Tests\NodeAnalyzer\Source\FooBar', $result);
    }

    /**
     * @test
     */
    public function it_resolves_a_model_named_after_the_table_the_framework_would_join_through(): void
    {
        $pivotClassAnalyzer = $this->make(PivotClassAnalyzer::class);

        $classMethod = $this->parseClassMethod('conventionalPivot', 'Post.php');

        $result = $pivotClassAnalyzer->resolvePivotClass(
            $classMethod,
            $this->resolveRelationCall($classMethod),
            'post_tag'
        );

        Assert::assertSame('RectorLaravel\Tests\NodeAnalyzer\Source\Pivots\PostTag', $result);
    }

    /**
     * @test
     */
    public function it_does_not_resolve_a_model_the_relation_joins(): void
    {
        $pivotClassAnalyzer = $this->make(PivotClassAnalyzer::class);

        $classMethod = $this->parseClassMethod('tags', 'Taggable.php');

        $result = $pivotClassAnalyzer->resolvePivotClass(
            $classMethod,
            $this->resolveRelationCall($classMethod),
            'taggables'
        );

        Assert::assertNull($result);
    }

    /**
     * @test
     */
    public function it_does_not_resolve_a_model_which_is_not_a_pivot_of_the_relation(): void
    {
        $pivotClassAnalyzer = $this->make(PivotClassAnalyzer::class);

        $classMethod = $this->parseClassMethod('unrelatedModelAsTable', 'Post.php');

        $result = $pivotClassAnalyzer->resolvePivotClass(
            $classMethod,
            $this->resolveRelationCall($classMethod),
            'users'
        );

        Assert::assertNull($result);
    }

    /**
     * @test
     */
    public function it_does_not_resolve_a_pivot_class_for_an_unknown_table(): void
    {
        $pivotClassAnalyzer = $this->make(PivotClassAnalyzer::class);

        $classMethod = $this->parseClassMethod('unknownTable', 'SomeModelWithPivotRelations.php');

        $result = $pivotClassAnalyzer->resolvePivotClass(
            $classMethod,
            $this->resolveRelationCall($classMethod),
            'no_such_table'
        );

        Assert::assertNull($result);
    }

    /**
     * @test
     */
    public function it_resolves_the_pivot_class_from_a_using_call(): void
    {
        $pivotClassAnalyzer = $this->make(PivotClassAnalyzer::class);

        $classMethod = $this->parseClassMethod('usingPivot', 'SomeModelWithPivotRelations.php');

        $result = $pivotClassAnalyzer->resolvePivotClass(
            $classMethod,
            $this->resolveRelationCall($classMethod),
            'custom_table'
        );

        Assert::assertSame(
            'RectorLaravel\Tests\NodeAnalyzer\Source\SomeModelWithCustomTableAndPrimaryKey',
            $result
        );
    }

    /**
     * @test
     */
    public function it_does_not_resolve_a_using_call_which_conflicts_with_the_table(): void
    {
        $pivotClassAnalyzer = $this->make(PivotClassAnalyzer::class);

        $classMethod = $this->parseClassMethod('conflictingUsingPivot', 'SomeModelWithPivotRelations.php');

        $result = $pivotClassAnalyzer->resolvePivotClass(
            $classMethod,
            $this->resolveRelationCall($classMethod),
            'foo_bar'
        );

        Assert::assertNull($result);
    }

    /**
     * @test
     */
    public function it_resolves_the_pivot_class_from_the_relation_generics(): void
    {
        $pivotClassAnalyzer = $this->make(PivotClassAnalyzer::class);

        $classMethod = $this->parseClassMethod('genericsPivot', 'SomeModelWithPivotRelations.php');

        $result = $pivotClassAnalyzer->resolvePivotClass(
            $classMethod,
            $this->resolveRelationCall($classMethod),
            'custom_table'
        );

        Assert::assertSame(
            'RectorLaravel\Tests\NodeAnalyzer\Source\SomeModelWithCustomTableAndPrimaryKey',
            $result
        );
    }

    private function parseClassMethod(string $methodName, string $file): ClassMethod
    {
        $rectorParser = $this->make(RectorParser::class);
        $nodeScopeAndMetadataDecorator = $this->make(NodeScopeAndMetadataDecorator::class);

        $filePath = __DIR__ . '/Source/Pivots/' . $file;

        $statements = $rectorParser->parseFile($filePath);
        $statements = $nodeScopeAndMetadataDecorator->decorateNodesFromFile($filePath, $statements);

        if (! $statements[0] instanceof Namespace_) {
            throw new Exception('Fixture nodes are incorrect');
        }

        foreach ($statements[0]->stmts as $statement) {
            if (! $statement instanceof ClassLike) {
                continue;
            }

            $classMethod = $statement->getMethod($methodName);

            if ($classMethod instanceof ClassMethod) {
                return $classMethod;
            }
        }

        throw new Exception('Fixture nodes are incorrect');
    }

    private function resolveReturnedCall(ClassMethod $classMethod): MethodCall
    {
        $statements = (array) $classMethod->stmts;

        if (! $statements[0] instanceof Return_ || ! $statements[0]->expr instanceof MethodCall) {
            throw new Exception('Fixture nodes are incorrect');
        }

        return $statements[0]->expr;
    }

    private function resolveRelationCall(ClassMethod $classMethod): MethodCall
    {
        $methodCall = $this->resolveReturnedCall($classMethod);

        while ($methodCall->var instanceof MethodCall) {
            $methodCall = $methodCall->var;
        }

        return $methodCall;
    }
}
