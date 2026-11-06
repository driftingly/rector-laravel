<?php

declare(strict_types=1);

namespace RectorLaravel\Tests\Rector\MethodCall\EloquentQueryToModelStaticMethodRector\Source;

class NotAModel
{
    public static function query(): self
    {
        return new self;
    }

    public function get(): array
    {
        return [];
    }
}
