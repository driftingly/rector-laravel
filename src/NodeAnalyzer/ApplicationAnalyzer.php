<?php

namespace RectorLaravel\NodeAnalyzer;

use ReflectionClassConstant;
use RuntimeException;

class ApplicationAnalyzer
{
    /**
     * @var class-string
     */
    private string $applicationClass = 'Illuminate\Foundation\Application';
    private ?string $version = null;

    /**
     * @param  class-string  $applicationClass
     */
    public function __construct(string $applicationClass = 'Illuminate\Foundation\Application')
    {
        $this->applicationClass = $applicationClass;
    }

    /**
     * @return static
     */
    public function setVersion(?string $version)
    {
        $this->version = $version;

        return $this;
    }

    /**
     * @param  class-string  $applicationClass
     * @return $this
     */
    public function setApplicationClass(string $applicationClass)
    {
        $this->applicationClass = $applicationClass;

        return $this;
    }

    /**
     * @return class-string
     */
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
