<?php

declare(strict_types=1);

namespace RectorLaravel\ValueObject;

final readonly class ObservedByRegistration
{
    /**
     * @param  list<string>  $observerClasses
     */
    public function __construct(
        public string $modelClass,
        public array $observerClasses,
    ) {}
}
