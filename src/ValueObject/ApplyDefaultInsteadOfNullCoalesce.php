<?php

namespace RectorLaravel\ValueObject;

use PHPStan\Type\ObjectType;

final readonly class ApplyDefaultInsteadOfNullCoalesce
{
    public function __construct(private string $methodName, private ?ObjectType $objectType = null,  private int $argumentPosition = 1)
    {
    }

    public function getObjectType(): ?ObjectType
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
