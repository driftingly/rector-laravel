<?php

namespace Illuminate\Database\Eloquent;

use Illuminate\Database\Eloquent\Concerns\HasRelationships;

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

    /**
     * @return \Illuminate\Database\Eloquent\Builder<static>
     */
    public static function query(): Builder
    {
        return new Builder;
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
        return $this->table ?? '<default_table_mechanism>';
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
}
