<?php

declare(strict_types=1);

namespace RectorLaravel\ValueObject;

final readonly class ServiceNameTypeAndVariableName
{
    public function __construct(
        private string $serviceName,
        private string $type,
        private string $variableName
    ) {
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
