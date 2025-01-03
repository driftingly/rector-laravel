<?php

declare(strict_types=1);

namespace RectorLaravel\ValueObject;

use PHPStan\Type\ObjectType;

final class TypeToTimeMethodAndPosition
{
    /**
     * @readonly
     * @var string
     */
    private $type;
    /**
     * @readonly
     * @var string
     */
    private $methodName;
    /**
     * @readonly
     * @var int
     */
    private $position;
    public function __construct(string $type, string $methodName, int $position)
    {
        $this->type = $type;
        $this->methodName = $methodName;
        $this->position = $position;
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
