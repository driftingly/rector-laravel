<?php
class SkipAbstractMethods extends \Illuminate\Database\Eloquent\Model
{
    abstract public function getStatusAttribute(): string;
}
