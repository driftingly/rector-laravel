<?php

namespace RectorLaravel\ValueObject;

use PhpParser\Node\Expr\ClassConstFetch;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Scalar\String_;
use Webmozart\Assert\Assert;

final readonly class ExpectedClassMethodMethodCalls
{
    /**
     * @param  MethodCall[]  $expectedMethodCalls
     * @param  list<String_|ClassConstFetch>  $expectedItems
     * @param  MethodCall[]  $notExpectedMethodCalls
     * @param  list<String_|ClassConstFetch>  $notExpectedItems
     */
    public function __construct(
        private array $expectedMethodCalls = [],
        private array $expectedItems = [],
        private array $notExpectedMethodCalls = [],
        private array $notExpectedItems = []
    ) {
        Assert::allIsInstanceOf($this->expectedMethodCalls, MethodCall::class);
        Assert::allIsInstanceOf($this->notExpectedMethodCalls, MethodCall::class);
        Assert::allIsInstanceOfAny($this->expectedItems, [String_::class, ClassConstFetch::class]);
        Assert::allIsInstanceOfAny($this->expectedItems, [String_::class, ClassConstFetch::class]);
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
     * @return array<int<0, max>, ClassConstFetch|String_>
     */
    public function getItemsToFake(): array
    {
        return array_unique(array_merge($this->expectedItems, $this->notExpectedItems), SORT_REGULAR);
    }

    /**
     * @return array<int<0, max>, ClassConstFetch|String_>
     */
    public function getExpectedItems(): array
    {
        return array_unique($this->expectedItems, SORT_REGULAR);
    }

    /**
     * @return array<int<0, max>, ClassConstFetch|String_>
     */
    public function getNotExpectedItems(): array
    {
        return array_unique($this->notExpectedItems, SORT_REGULAR);
    }
}
