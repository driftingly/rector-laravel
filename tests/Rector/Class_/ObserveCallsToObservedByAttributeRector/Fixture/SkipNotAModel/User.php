<?php

declare(strict_types=1);

namespace RectorLaravel\Tests\Rector\Class_\ObserveCallsToObservedByAttributeRector\Fixture\SkipNotAModel;

final class User
{
    public static function observe(string $observer): void {}
}
