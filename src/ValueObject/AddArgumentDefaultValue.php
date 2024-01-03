<?php

declare(strict_types=1);

namespace RectorLaravel\ValueObject;

use PHPStan\Type\ObjectType;

final readonly class AddArgumentDefaultValue
{
    public function __construct(
        private string $class,
        private string $method,
        private int $position,
        private mixed $defaultValue
    ) {
    }

    public function getObjectType(): ObjectType
    {
        return new ObjectType($this->class);
    }

    public function getMethod(): string
    {
        return $this->method;
    }

    public function getPosition(): int
    {
        return $this->position;
    }

    public function getDefaultValue(): mixed
    {
        return $this->defaultValue;
    }
}
