<?php

declare(strict_types=1);

namespace RectorLaravel\Rector\ClassMethod;

use Rector\VersionBonding\ValueObject\ComposerPackageConstraint;
use RectorLaravel\Tests\Rector\ClassMethod\AddGenericReturnTypeToRelationsRector\AddGenericReturnTypeToRelationsRectorTest;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * Adds generics in the Laravel 12.3+ format, using `$this` as the child generic and the Pivot generic for
 * BelongsToMany relations.
 *
 * @see AddGenericReturnTypeToRelationsRectorTest
 */
final class AddGenericReturnTypeToRelationsRector extends AbstractAddGenericReturnTypeToRelationsRector
{
    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(
            'Add generic return type to relations in child of Illuminate\Database\Eloquent\Model',
            [
                new CodeSample(
                    <<<'CODE_SAMPLE'
use App\Account;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Model
{
    public function accounts(): HasMany
    {
        return $this->hasMany(Account::class);
    }
}
CODE_SAMPLE

                    ,
                    <<<'CODE_SAMPLE'
use App\Account;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Model
{
    /** @return HasMany<Account, $this> */
    public function accounts(): HasMany
    {
        return $this->hasMany(Account::class);
    }
}
CODE_SAMPLE
                ),
            ]
        );
    }

    /**
     * @return ComposerPackageConstraint
     */
    public function provideComposerPackageConstraint()
    {
        return new ComposerPackageConstraint('laravel/framework', '>=12.3');
    }

    protected function shouldUseNewGenerics(): bool
    {
        return true;
    }

    protected function shouldUsePivotGeneric(): bool
    {
        return true;
    }
}
