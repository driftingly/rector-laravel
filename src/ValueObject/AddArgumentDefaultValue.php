<?php

declare(strict_types=1);

namespace Rector\Laravel\ValueObject;

use PHPStan\Type\ObjectType;

final class AddArgumentDefaultValue
{
    /**
     * @param mixed $defaultValue
     */
    public function __construct(
        private readonly string $class,
        private readonly string $method,
        private readonly int $position,
        private $defaultValue
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
