<?php

namespace Illuminate\Foundation\Auth;

use Illuminate\Database\Eloquent\Model;

if (class_exists('Illuminate\Foundation\Auth\User')) {
    return;
}

class User extends Model {}
