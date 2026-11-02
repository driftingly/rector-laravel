<?php

declare(strict_types=1);

namespace RectorLaravel\Rector\ClassMethod;

use Rector\RuleDoc\CodeSample\CodeSample;
use Rector\RuleDoc\RuleDefinition;
use Rector\VersionBonding\ValueObject\ComposerPackageConstraint;
use RectorLaravel\Tests\Rector\ClassMethod\AddGenericReturnTypeToRelationsRector\AddNewGenericReturnTypeToRelationsRectorTest;

/**
 * Adds generics in the Laravel 11.15+ format, using `$this` as the child generic, but without the Pivot generic
 * that BelongsToMany relations gained in 12.3.
 *
 * @see AddNewGenericReturnTypeToRelationsRectorTest
 */
final class AddNewGenericReturnTypeToRelationsRector extends AbstractAddGenericReturnTypeToRelationsRector
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
        return new ComposerPackageConstraint('laravel/framework', '>=11.15 <12.3');
    }

    protected function shouldUseNewGenerics(): bool
    {
        return true;
    }

    protected function shouldUsePivotGeneric(): bool
    {
        return false;
    }
}
