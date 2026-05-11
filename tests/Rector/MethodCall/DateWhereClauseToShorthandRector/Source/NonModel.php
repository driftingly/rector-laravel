<?php

namespace RectorLaravel\Tests\Rector\MethodCall\DateWhereClauseToShorthandRector\Source;

final class NonModel
{
    public static function where(string $column, string $operator, mixed $value): void {}

    public static function whereDate(string $column, string $operator, mixed $value): void {}
}
