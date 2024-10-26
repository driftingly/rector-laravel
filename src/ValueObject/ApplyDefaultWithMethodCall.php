<?php

namespace RectorLaravel\ValueObject;

use PHPStan\Type\ObjectType;

final readonly class ApplyDefaultWithMethodCall
{
    public function __construct(private ObjectType $objectType, private string $methodName, private int $argumentPosition = 1)
    {
    }

    public function getObjectType(): ObjectType
    {
        return $this->objectType;
    }

    public function getMethodName(): string
    {
        return $this->methodName;
    }

    public function getArgumentPosition(): int
    {
        return $this->argumentPosition;
    }
}
