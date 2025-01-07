<?php

namespace RectorLaravel\Tests\Unit\ReflectionAnalyzer;

use PHPUnit\Framework\Assert;
use Rector\Testing\PHPUnit\AbstractLazyTestCase;
use RectorLaravel\ReflectionAnalyzer\ModelAnalyzer;

class ModelAnalyzerTest extends AbstractLazyTestCase
{
    public function test_it_can_retrieve_the_table_name(): void
    {
        $analyzer = $this->make(ModelAnalyzer::class);

        $result = $analyzer->getTable('RectorLaravel\Tests\Fixtures\ReflectionAnalyzer\FooBar');

        Assert::assertSame('<default_table_mechanism>', $result);
    }

    public function test_it_can_retrieve_the_key_name(): void
    {
        $analyzer = $this->make(ModelAnalyzer::class);

        $result = $analyzer->getPrimaryKey('RectorLaravel\Tests\Fixtures\ReflectionAnalyzer\FooBar');

        Assert::assertSame('id', $result);
    }
}
