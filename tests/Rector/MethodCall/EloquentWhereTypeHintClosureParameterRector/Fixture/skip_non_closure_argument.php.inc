<?php

/** @var \Illuminate\Contracts\Database\Query\Builder $query */
$query->where('name', 'a');

class User extends \Illuminate\Database\Eloquent\Model
{

}

User::where('name', 'a');

?>
