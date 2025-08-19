<?php

declare(strict_types=1);

namespace RectorLaravel\ValueObject;

final class ServiceNameTypeAndVariableName
{
    /**
     * @readonly
     */
    private string $serviceName;
    /**
     * @readonly
     */
    private string $type;
    /**
     * @readonly
     */
    private string $variableName;
    public function __construct(string $serviceName, string $type, string $variableName)
    {
        $this->serviceName = $serviceName;
        $this->type = $type;
        $this->variableName = $variableName;
    }

    public function getServiceName(): string
    {
        return $this->serviceName;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getVariableName(): string
    {
        return $this->variableName;
    }
}
