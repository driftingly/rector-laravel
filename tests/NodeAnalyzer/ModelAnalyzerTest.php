<?php

namespace RectorLaravel\Tests\NodeAnalyzer;

use PHPUnit\Framework\Assert;
use Rector\NodeTypeResolver\PHPStan\Scope\ScopeFactory;
use Rector\Testing\PHPUnit\AbstractLazyTestCase;
use RectorLaravel\NodeAnalyzer\ModelAnalyzer;

class ModelAnalyzerTest extends AbstractLazyTestCase
{
    /**
     * @test
     */
    public function it_can_retrieve_the_table_name(): void
    {
        $modelAnalyzer = $this->make(ModelAnalyzer::class);

        $result = $modelAnalyzer->getTable('RectorLaravel\Tests\NodeAnalyzer\Source\SomeModel');

        Assert::assertSame('<default_table_mechanism>', $result);
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
    public function it_can_determine_if_the_model_uses_the_scope(): void
    {
        $modelAnalyzer = $this->make(ModelAnalyzer::class);
        $scopeFactory = $this->make(ScopeFactory::class);
        $mutatingScope = $scopeFactory->createFromFile(__DIR__ . '/Source/SomeModel.php');

        $result = $modelAnalyzer->isQueryScopeOnModel(
            'RectorLaravel\Tests\NodeAnalyzer\Source\SomeModel',
            'someScope',
            $mutatingScope,
        );

        $this->assertTrue($result);

        $result = $modelAnalyzer->isQueryScopeOnModel(
            'RectorLaravel\Tests\NodeAnalyzer\Source\SomeModel',
            'filterSomething',
            $mutatingScope,
        );

        $this->assertTrue($result);

        $result = $modelAnalyzer->isQueryScopeOnModel(
            'RectorLaravel\Tests\NodeAnalyzer\Source\SomeModel',
            'someGenericFunction',
            $mutatingScope,
        );

        $this->assertFalse($result);

        $result = $modelAnalyzer->isQueryScopeOnModel(
            'RectorLaravel\Tests\NodeAnalyzer\Source\SomeModel',
            'nonExistingMethod',
            $mutatingScope,
        );

        $this->assertFalse($result);
    }
}
