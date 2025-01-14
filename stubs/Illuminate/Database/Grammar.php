<?php

namespace Illuminate\Database;

if (class_exists('Illuminate\Database\Grammar')) {
    return;
}

abstract class Grammar {}
