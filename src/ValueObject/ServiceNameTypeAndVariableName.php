<?php

declare(strict_types=1);

namespace RectorLaravel\ValueObject;

final class ServiceNameTypeAndVariableName
{
    public function __construct(
        private readonly string $serviceName,
        private readonly string $type,
        private readonly string $variableName
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
