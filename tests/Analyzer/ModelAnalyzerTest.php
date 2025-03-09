<?php

namespace RectorLaravel\Tests\Analyzer;

use PHPUnit\Framework\Assert;
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

        $result = $modelAnalyzer->getTable('RectorLaravel\Tests\Analyzer\Source\SomeModel');

        Assert::assertSame('<default_table_mechanism>', $result);
    }

    /**
     * @test
     */
    public function it_can_retrieve_the_custom_table_name(): void
    {
        $modelAnalyzer = $this->make(ModelAnalyzer::class);

        $result = $modelAnalyzer->getTable('RectorLaravel\Tests\Analyzer\Source\SomeModelWithCustomTableAndPrimaryKey');

        Assert::assertSame('custom_table', $result);
    }

    /**
     * @test
     */
    public function it_can_retrieve_the_key_name(): void
    {
        $modelAnalyzer = $this->make(ModelAnalyzer::class);

        $result = $modelAnalyzer->getPrimaryKey('RectorLaravel\Tests\Analyzer\Source\SomeModel');

        Assert::assertSame('id', $result);
    }

    /**
     * @test
     */
    public function it_can_retrieve_the_custom_key_name(): void
    {
        $modelAnalyzer = $this->make(ModelAnalyzer::class);

        $result = $modelAnalyzer->getPrimaryKey('RectorLaravel\Tests\Analyzer\Source\SomeModelWithCustomTableAndPrimaryKey');

        Assert::assertSame('uuid', $result);
    }
}
