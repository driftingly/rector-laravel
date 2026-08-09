<?php

namespace RectorLaravel\Tests\NodeAnalyzer;

use PHPStan\Type\ObjectType;
use PHPUnit\Framework\Assert;
use Rector\NodeTypeResolver\PHPStan\Scope\ScopeFactory;
use Rector\Testing\PHPUnit\AbstractLazyTestCase;
use RectorLaravel\NodeAnalyzer\ModelAnalyzer;

final class ModelAnalyzerTest extends AbstractLazyTestCase
{
    /**
     * @test
     */
    public function it_can_retrieve_the_table_name(): void
    {
        $modelAnalyzer = $this->make(ModelAnalyzer::class);

        $result = $modelAnalyzer->getTable('RectorLaravel\Tests\NodeAnalyzer\Source\SomeModel');

        Assert::assertSame('some_models', $result);
    }

    /**
     * @test
     */
    public function it_can_retrieve_the_custom_table_name(): void
    {
        $modelAnalyzer = $this->make(ModelAnalyzer::class);

        $result = $modelAnalyzer->getTable('RectorLaravel\Tests\NodeAnalyzer\Source\SomeModelWithCustomTableAndPrimaryKey');

        Assert::assertSame('custom_table', $result);
    }

    /**
     * @test
     */
    public function it_can_retrieve_the_table_name_from_the_table_attribute(): void
    {
        $modelAnalyzer = $this->make(ModelAnalyzer::class);

        $result = $modelAnalyzer->getTable('RectorLaravel\Tests\NodeAnalyzer\Source\SomeModelWithTableAttribute');

        Assert::assertSame('attribute_table', $result);
    }

    /**
     * @test
     */
    public function it_can_retrieve_the_table_name_from_a_positional_table_attribute(): void
    {
        $modelAnalyzer = $this->make(ModelAnalyzer::class);

        $result = $modelAnalyzer->getTable('RectorLaravel\Tests\NodeAnalyzer\Source\SomeModelWithPositionalTableAttribute');

        Assert::assertSame('positional_attribute_table', $result);
    }

    /**
     * @test
     */
    public function it_can_retrieve_the_table_name_from_the_attribute_of_an_unconstructable_model(): void
    {
        $modelAnalyzer = $this->make(ModelAnalyzer::class);

        $result = $modelAnalyzer->getTable('RectorLaravel\Tests\NodeAnalyzer\Source\SomeUnconstructableModelWithTableAttribute');

        Assert::assertSame('unconstructable_attribute_table', $result);
    }

    /**
     * @test
     */
    public function it_can_retrieve_the_table_name_from_an_object_type(): void
    {
        $modelAnalyzer = $this->make(ModelAnalyzer::class);

        $result = $modelAnalyzer->getTable(
            new ObjectType('RectorLaravel\Tests\NodeAnalyzer\Source\SomeModelWithCustomTableAndPrimaryKey')
        );

        Assert::assertSame('custom_table', $result);
    }

    /**
     * @test
     */
    public function it_can_retrieve_the_key_name(): void
    {
        $modelAnalyzer = $this->make(ModelAnalyzer::class);

        $result = $modelAnalyzer->getPrimaryKey('RectorLaravel\Tests\NodeAnalyzer\Source\SomeModel');

        Assert::assertSame('id', $result);
    }

    /**
     * @test
     */
    public function it_can_retrieve_the_custom_key_name(): void
    {
        $modelAnalyzer = $this->make(ModelAnalyzer::class);

        $result = $modelAnalyzer->getPrimaryKey('RectorLaravel\Tests\NodeAnalyzer\Source\SomeModelWithCustomTableAndPrimaryKey');

        Assert::assertSame('uuid', $result);
    }

    /**
     * @test
     */
    public function it_can_determine_if_the_model_has_query_scope(): void
    {
        $modelAnalyzer = $this->make(ModelAnalyzer::class);
        $scopeFactory = $this->make(ScopeFactory::class);
        $mutatingScope = $scopeFactory->createFromFile(__DIR__ . '/Source/SomeModel.php');

        $result = $modelAnalyzer->isQueryScopeOnModel(
            'RectorLaravel\Tests\NodeAnalyzer\Source\SomeModel',
            'someScope',
            $mutatingScope,
        );

        Assert::assertTrue($result);

        $result = $modelAnalyzer->isQueryScopeOnModel(
            'RectorLaravel\Tests\NodeAnalyzer\Source\SomeModel',
            'filterSomething',
            $mutatingScope,
        );

        Assert::assertTrue($result);

        $result = $modelAnalyzer->isQueryScopeOnModel(
            'RectorLaravel\Tests\NodeAnalyzer\Source\SomeModel',
            'someGenericFunction',
            $mutatingScope,
        );

        Assert::assertFalse($result);

        $result = $modelAnalyzer->isQueryScopeOnModel(
            'RectorLaravel\Tests\NodeAnalyzer\Source\SomeModel',
            'nonExistingMethod',
            $mutatingScope,
        );

        Assert::assertFalse($result);
    }

    /**
     * @test
     */
    public function it_can_determine_if_the_model_has_relationship_method(): void
    {
        $modelAnalyzer = $this->make(ModelAnalyzer::class);
        $scopeFactory = $this->make(ScopeFactory::class);
        $mutatingScope = $scopeFactory->createFromFile(__DIR__ . '/Source/SomeModel.php');

        $result = $modelAnalyzer->isRelationshipOnModel(
            'RectorLaravel\Tests\NodeAnalyzer\Source\SomeModel',
            'relationship',
            $mutatingScope,
        );

        Assert::assertTrue($result);

        $result = $modelAnalyzer->isRelationshipOnModel(
            'RectorLaravel\Tests\NodeAnalyzer\Source\SomeModel',
            'notRelationship',
            $mutatingScope,
        );

        Assert::assertFalse($result);
    }
}
