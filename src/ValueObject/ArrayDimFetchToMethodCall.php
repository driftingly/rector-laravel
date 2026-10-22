<?php

declare(strict_types=1);

namespace RectorLaravel\ValueObject;

use PHPStan\Type\ObjectType;

final readonly class ArrayDimFetchToMethodCall
{
    public function __construct(
        private ObjectType $objectType,
        private string $method,
        // Optional methods for set, exists, and unset operations
        // if null, then these operations will not be transformed
        private ?string $setMethod = null,
        private ?string $existsMethod = null,
        private ?string $unsetMethod = null,
    ) {}

    public function getObjectType(): ObjectType
    {
        return $this->objectType;
    }

    public function getMethod(): string
    {
        return $this->method;
    }

    public function getSetMethod(): ?string
    {
        return $this->setMethod;
    }

    public function getExistsMethod(): ?string
    {
        return $this->existsMethod;
    }

    public function getUnsetMethod(): ?string
    {
        return $this->unsetMethod;
    }
}
