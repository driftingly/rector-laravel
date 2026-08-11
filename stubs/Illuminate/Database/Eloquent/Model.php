<?php

namespace Illuminate\Database\Eloquent;

use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Concerns\HasRelationships;
use Illuminate\Support\Str;
use ReflectionClass;

if (class_exists('Illuminate\Database\Eloquent\Model')) {
    return;
}

/**
 * @method static creating(\Closure $closure)
 */
abstract class Model
{
    use HasRelationships;

    /**
     * The table associated with the model.
     *
     * @var string|null
     */
    protected $table;

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'id';

    public function __construct()
    {
        $this->initializeModelAttributes();
    }

    /**
     * @return Builder<static>
     */
    public static function query(): Builder
    {
        return new Builder;
    }

    /**
     * The framework resolves the Table attribute into the table and primary key properties when the model is
     * constructed. A table declared on the model itself takes precedence over the attribute, and the primary
     * key is only taken from the attribute while the model still uses the default one.
     */
    public function initializeModelAttributes(): void
    {
        $reflection = new ReflectionClass(static::class);

        $table = $this->resolveTableAttribute($reflection);

        $declaresTable = $reflection->hasProperty('table')
            && $reflection->getProperty('table')->getDeclaringClass()->getName() === static::class;

        if (! $declaresTable && $reflection->getAttributes(Table::class) !== []) {
            $this->table = $table->name ?? null;
        } else {
            $this->table ??= $table->name ?? null;
        }

        if ($this->primaryKey === 'id' && $table !== null && $table->key !== null) {
            $this->primaryKey = $table->key;
        }
    }

    /**
     * Exists in the Illuminate/Database/Eloquent/Concerns/HasTimestamps trait
     * Put here for simplicity
     */
    public function touch($attribute = null)
    {
        return true;
    }

    public function getTable()
    {
        return $this->table ?? Str::snake(Str::pluralStudly(Str::afterLast(static::class, '\\')));
    }

    /**
     * Get the primary key for the model.
     *
     * @return string
     */
    public function getKeyName()
    {
        return $this->primaryKey;
    }

    /**
     * The attribute is looked for on the parents and traits of the model as well.
     *
     * @param  ReflectionClass<object>  $reflection
     */
    private function resolveTableAttribute(ReflectionClass $reflection): ?Table
    {
        do {
            foreach ([$reflection, ...array_values($reflection->getTraits())] as $reflectionWithAttributes) {
                $attributes = $reflectionWithAttributes->getAttributes(Table::class);

                if ($attributes !== []) {
                    return $attributes[0]->newInstance();
                }
            }
        } while ($reflection = $reflection->getParentClass());

        return null;
    }
}
