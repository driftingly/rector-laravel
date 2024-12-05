<?php

namespace RectorLaravel\ValueObject;

use PHPStan\Type\ObjectType;

final class ApplyDefaultInsteadOfNullCoalesce
{
    /**
     * @readonly
     * @var string
     */
    private $methodName;
    /**
     * @readonly
     * @var \PHPStan\Type\ObjectType|null
     */
    private $objectType;
    /**
     * @readonly
     * @var int
     */
    private $argumentPosition = 1;
    public function __construct(string $methodName, ?ObjectType $objectType = null, int $argumentPosition = 1)
    {
        $this->methodName = $methodName;
        $this->objectType = $objectType;
        $this->argumentPosition = $argumentPosition;
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
