<?php

namespace RectorLaravel\ValueObject;

use PhpParser\Node\Expr\MethodCall;
use Webmozart\Assert\Assert;

final readonly class ExpectedClassMethodMethodCalls
{
    /**
     * @param MethodCall[] $expectedMethodCalls
     * @param class-string[] $expectedItems
     * @param MethodCall[] $notExpectedMethodCalls
     * @param class-string[] $notExpectedItems
     */
    public function __construct(
        private array $expectedMethodCalls = [],
        private array $expectedItems = [],
        private array $notExpectedMethodCalls = [],
        private array $notExpectedItems = []
    )
    {
        Assert::allIsInstanceOf($this->expectedMethodCalls, MethodCall::class);
        Assert::allIsInstanceOf($this->notExpectedMethodCalls, MethodCall::class);
        Assert::allString($this->expectedItems);
        Assert::allString($this->notExpectedItems);
    }

    public function isActionable(): bool
    {
        return ! ($this->expectedMethodCalls === [] && $this->notExpectedMethodCalls === []);
    }

    /**
     * @return MethodCall[]
     */
    public function getAllMethodCalls(): array
    {
        return array_merge($this->expectedMethodCalls, $this->notExpectedMethodCalls);
    }

    /**
     * @return MethodCall[]
     */
    public function getNotExpectedMethodCalls(): array
    {
        return $this->notExpectedMethodCalls;
    }

    /**
     * @return class-string[]
     */
    public function getItemsToFake(): array
    {
        return array_unique(array_merge($this->expectedItems, $this->notExpectedItems));
    }

    /**
     * @return class-string[]
     */
    public function getExpectedItems(): array
    {
        return array_unique($this->expectedItems);
    }

    /**
     * @return class-string[]
     */
    public function getNotExpectedItems(): array
    {
        return array_unique($this->notExpectedItems);
    }
}
