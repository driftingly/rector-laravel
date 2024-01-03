<?php

declare(strict_types=1);

namespace RectorLaravel\ValueObject;

use PHPStan\Type\ObjectType;

final readonly class TypeToTimeMethodAndPosition
{
    public function __construct(
        private string $type,
        private string $methodName,
        private int $position
    ) {
    }

    public function getObjectType(): ObjectType
    {
        return new ObjectType($this->type);
    }

    public function getMethodName(): string
    {
        return $this->methodName;
    }

    public function getPosition(): int
    {
        return $this->position;
    }
}
