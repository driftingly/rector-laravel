<?php

declare(strict_types=1);

namespace RectorLaravel\ValueObject;

use PHPStan\Type\ObjectType;

final class AddArgumentDefaultValue
{
    /**
     * @readonly
     * @var string
     */
    private $class;
    /**
     * @readonly
     * @var string
     */
    private $method;
    /**
     * @readonly
     * @var int
     */
    private $position;
    /**
     * @readonly
     * @var mixed
     */
    private $defaultValue;
    /**
     * @param mixed $defaultValue
     */
    public function __construct(string $class, string $method, int $position, $defaultValue)
    {
        $this->class = $class;
        $this->method = $method;
        $this->position = $position;
        $this->defaultValue = $defaultValue;
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

    /**
     * @return mixed
     */
    public function getDefaultValue()
    {
        return $this->defaultValue;
    }
}
