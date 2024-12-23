<?php

namespace RectorLaravel\ValueObject;

use Webmozart\Assert\Assert;

final readonly class ReplaceRequestKeyAndMethodValue
{
    public function __construct(private string $key, private string $method)
    {
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
