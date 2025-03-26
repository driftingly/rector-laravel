<?php

namespace RectorLaravel\NodeAnalyzer;

use ReflectionClassConstant;
use RuntimeException;

class ApplicationAnalyzer
{
    private ?string $version = null;

    public function __construct(
        private string $applicationClass = 'Illuminate\Foundation\Application',
    ) {}

    public function setVersion(?string $version): static
    {
        $this->version = $version;

        return $this;
    }

    public function setApplicationClass(string $applicationClass): static
    {
        $this->applicationClass = $applicationClass;

        return $this;
    }

    public function getApplicationClass(): string
    {
        return $this->applicationClass;
    }

    /**
     * @param  '>='|'='|'<='  $comparison
     */
    public function isVersion(string $comparison, string $version): bool
    {
        return version_compare($this->getVersion(), $version, $comparison);
    }

    public function getVersion(): string
    {
        if ($this->version !== null) {
            return $this->version;
        }

        $reflectionClassConstant = new ReflectionClassConstant($this->applicationClass, 'VERSION');

        if (! is_string($version = $reflectionClassConstant->getValue())) {
            throw new RuntimeException('expected VERSION to be a string, got ' . gettype($version));
        }

        return $version;
    }
}
