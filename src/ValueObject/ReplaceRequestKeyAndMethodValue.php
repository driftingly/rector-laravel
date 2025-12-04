<?php

namespace RectorLaravel\ValueObject;

use Webmozart\Assert\Assert;

final class ReplaceRequestKeyAndMethodValue
{
    /**
     * @readonly
     */
    private string $key;
    /**
     * @readonly
     */
    private string $method;
    public function __construct(string $key, string $method)
    {
        $this->key = $key;
        $this->method = $method;
        Assert::inArray($this->method, ['query', 'post', 'input']);
    }

    public function getKey(): string
    {
        return $this->key;
    }

    public function getMethod(): string
    {
        return $this->method;
    }
}
