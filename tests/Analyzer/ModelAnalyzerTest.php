<?php

namespace RectorLaravel\Tests\Analyzer;

use PHPUnit\Framework\Assert;
use Rector\Testing\PHPUnit\AbstractLazyTestCase;
use RectorLaravel\NodeAnalyzer\ModelAnalyzer;

class ModelAnalyzerTest extends AbstractLazyTestCase
{
    public function test_it_can_retrieve_the_table_name(): void
    {
        $analyzer = $this->make(ModelAnalyzer::class);

        $result = $analyzer->getTable('RectorLaravel\Tests\Analyzer\Source\SomeModel');

        Assert::assertSame('<default_table_mechanism>', $result);
    }

    public function test_it_can_retrieve_the_key_name(): void
    {
        $analyzer = $this->make(ModelAnalyzer::class);

        $result = $analyzer->getPrimaryKey('RectorLaravel\Tests\Analyzer\Source\SomeModel');

        Assert::assertSame('id', $result);
    }
}
