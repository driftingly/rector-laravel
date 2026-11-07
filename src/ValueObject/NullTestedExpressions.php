<?php

declare(strict_types=1);

namespace RectorLaravel\ValueObject;

use PhpParser\Node\Expr;

use function spl_object_id;

/**
 * The expressions of one scope whose null-ness is observed: compared to null, coalesced, used as a condition, returned...
 */
final readonly class NullTestedExpressions
{
    /**
     * @param  array<int, true>  $exprIds  object ids of the tested expressions
     */
    public function __construct(
        private array $exprIds,
    ) {}

    public function contains(Expr $expr): bool
    {
        return isset($this->exprIds[spl_object_id($expr)]);
    }
}
