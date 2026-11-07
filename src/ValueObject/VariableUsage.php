<?php

declare(strict_types=1);

namespace RectorLaravel\ValueObject;

use PhpParser\Node\Expr;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\Variable;
use Webmozart\Assert\Assert;

use function is_string;
use function spl_object_id;

/**
 * How the variables of one scope are used: what a `refresh()` in place could still be observed by.
 */
final readonly class VariableUsage
{
    /**
     * @param  array<string, list<Variable>>  $occurrences  every occurrence of a variable, by name
     * @param  array<string, list<Variable>>  $readOccurrences  the occurrences that are not the receiver of a `fresh()` / `refresh()`
     * @param  array<int, string>  $assignedNames  object id of an assignment's right-hand side => assigned variable
     * @param  array<int, true>  $consumedOnTheSpotIds  `Variable`s that hand nothing of the object to anyone: an assignment
     *                                                  target, or a receiver dereferenced on the spot (`$model->status`)
     * @param  array<int, true>  $aliasedReceiverIds  `Variable`s whose method call result is used as a value: it may be the variable itself
     * @param  array<int, true>  $resultConsumedCallIds  method calls whose result is discarded or read on the spot (`->status`)
     * @param  array<string, true>  $locallyCreatedNames  variables only ever assigned a brand-new instance, never shared
     * @param  array<int, true>  $nodeIdsInsideClosures
     * @param  array<int, true>  $nodeIdsInsideLoops
     */
    public function __construct(
        private array $occurrences,
        private array $readOccurrences,
        private array $assignedNames,
        private array $consumedOnTheSpotIds,
        private array $aliasedReceiverIds,
        private array $resultConsumedCallIds,
        private array $locallyCreatedNames,
        private array $nodeIdsInsideClosures,
        private array $nodeIdsInsideLoops,
    ) {}

    public function isLocallyCreated(Variable $variable): bool
    {
        return is_string($variable->name) && isset($this->locallyCreatedNames[$variable->name]);
    }

    /**
     * The variable the given expression is assigned to, if any.
     */
    public function assignedVariableName(Expr $expr): ?string
    {
        return $this->assignedNames[spl_object_id($expr)] ?? null;
    }

    /**
     * @return list<Variable>
     */
    public function occurrencesOf(string $name): array
    {
        return $this->occurrences[$name] ?? [];
    }

    /**
     * True when the receiver may still be observed once `refresh()` has overwritten it in place.
     *
     * Source order is not enough, a variable is an object handle: only an earlier use that hands
     * nothing of the object to anyone is harmless. Everything later, and everything inside a loop
     * or a closure, blocks the rewrite. Other reloads of the variable are ignored when the result is
     * read on the spot, and counted when it is held (`$a = $job->fresh()`).
     */
    public function isObservedAfterwards(MethodCall $reload): bool
    {
        $receiver = $reload->var;
        Assert::isInstanceOf($receiver, Variable::class);
        Assert::string($receiver->name);

        $callPosition = $reload->getStartTokenPos();
        $isInsideLoop = isset($this->nodeIdsInsideLoops[spl_object_id($reload)]);
        $isResultHeld = ! isset($this->resultConsumedCallIds[spl_object_id($reload)]);

        if ($isResultHeld && isset($this->nodeIdsInsideClosures[spl_object_id($reload)])) {
            return true;
        }

        $occurrences = $isResultHeld ? $this->occurrencesOf($receiver->name) : ($this->readOccurrences[$receiver->name] ?? []);
        foreach ($occurrences as $occurrence) {
            if ($occurrence === $receiver) {
                continue;
            }

            if (! $this->isHarmlessEarlierUse($occurrence, $callPosition, $isInsideLoop)) {
                return true;
            }
        }

        return false;
    }

    private function isHarmlessEarlierUse(Variable $occurrence, int $callPosition, bool $isInsideLoop): bool
    {
        $id = spl_object_id($occurrence);
        if ($isInsideLoop || isset($this->nodeIdsInsideClosures[$id]) || isset($this->aliasedReceiverIds[$id])) {
            return false;
        }

        $position = $occurrence->getStartTokenPos();
        if ($position < 0 || $callPosition < 0 || $position > $callPosition) {
            return false;
        }

        return isset($this->consumedOnTheSpotIds[$id]);
    }
}
