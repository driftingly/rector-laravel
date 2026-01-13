<?php

declare(strict_types=1);

namespace RectorLaravel\Rector\Class_;

use PhpParser\Node;
use PhpParser\Node\Name;
use PhpParser\Node\Name\FullyQualified;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\TraitUse;
use RectorLaravel\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \RectorLaravel\Tests\Rector\Class_\ReplaceQueueTraitsWithQueueableRector\ReplaceQueueTraitsWithQueueableRectorTest
 */
final class ReplaceQueueTraitsWithQueueableRector extends AbstractRector
{
    private const string DISPATCHABLE_TRAIT = 'Illuminate\Foundation\Bus\Dispatchable';

    private const string INTERACTS_WITH_QUEUE_TRAIT = 'Illuminate\Queue\InteractsWithQueue';

    private const string QUEUEABLE_BY_BUS_TRAIT = 'Illuminate\Bus\Queueable';

    private const string SERIALIZES_MODELS_TRAIT = 'Illuminate\Queue\SerializesModels';

    private const string QUEUEABLE_TRAIT = 'Illuminate\Foundation\Queue\Queueable';

    private const array TRAITS_TO_REPLACE = [
        self::DISPATCHABLE_TRAIT,
        self::INTERACTS_WITH_QUEUE_TRAIT,
        self::QUEUEABLE_BY_BUS_TRAIT,
        self::SERIALIZES_MODELS_TRAIT,
    ];

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(
            'Replace Dispatchable, InteractsWithQueue, Queueable, and SerializesModels traits with the Queueable trait',
            [new CodeSample(
                <<<'CODE_SAMPLE'
use Illuminate\Bus\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SomeJob
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
}
CODE_SAMPLE,
                <<<'CODE_SAMPLE'
use Illuminate\Foundation\Queue\Queueable;

class SomeJob
{
    use Queueable;
}
CODE_SAMPLE
            )]
        );
    }

    /**
     * @return array<class-string<Node>>
     */
    public function getNodeTypes(): array
    {
        return [Class_::class];
    }

    /**
     * @param  Class_  $node
     */
    public function refactor(Node $node): ?Node
    {
        $traitUses = $node->getTraitUses();

        if ($traitUses === []) {
            return null;
        }

        $foundTraits = $this->findQueueTraits($traitUses);

        if (count($foundTraits) !== 4) {
            return null;
        }

        $this->replaceTraitsWithQueueable($node, $traitUses, $foundTraits);

        return $node;
    }

    /**
     * @param  TraitUse[]  $traitUses
     * @return array<string, array{traitUse: TraitUse, traitIndex: int}>
     */
    private function findQueueTraits(array $traitUses): array
    {
        $foundTraits = [];

        foreach ($traitUses as $traitUse) {
            foreach ($traitUse->traits as $traitIndex => $trait) {
                foreach (self::TRAITS_TO_REPLACE as $traitToReplace) {
                    if ($this->isName($trait, $traitToReplace)) {
                        $foundTraits[$traitToReplace] = [
                            'traitUse' => $traitUse,
                            'traitIndex' => $traitIndex,
                        ];
                    }
                }
            }
        }

        return $foundTraits;
    }

    /**
     * @param  TraitUse[]  $traitUses
     * @param  array<string, array{traitUse: TraitUse, traitIndex: int}>  $foundTraits
     */
    private function replaceTraitsWithQueueable(Class_ $class, array $traitUses, array $foundTraits): void
    {
        $replacedFirst = false;

        foreach ($traitUses as $traitUseIndex => $traitUse) {
            $newTraits = [];

            foreach ($traitUse->traits as $traitIndex => $trait) {
                $isQueueTrait = false;

                foreach (self::TRAITS_TO_REPLACE as $traitToReplace) {
                    if ($this->isName($trait, $traitToReplace)) {
                        $isQueueTrait = true;
                        break;
                    }
                }

                if ($isQueueTrait) {
                    if (! $replacedFirst) {
                        $newTraits[] = new FullyQualified(self::QUEUEABLE_TRAIT);
                        $replacedFirst = true;
                    }
                } else {
                    $newTraits[] = $trait;
                }
            }

            if ($newTraits === []) {
                unset($class->stmts[array_search($traitUse, $class->stmts, true)]);
                $class->stmts = array_values($class->stmts);
            } else {
                $traitUse->traits = $this->sortTraits($newTraits);
            }
        }
    }

    /**
     * @param  Name[]  $traits
     * @return Name[]
     */
    private function sortTraits(array $traits): array
    {
        usort($traits, function (Name $a, Name $b): int {
            return strcmp($a->toString(), $b->toString());
        });

        return $traits;
    }
}
