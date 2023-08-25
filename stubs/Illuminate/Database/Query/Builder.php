<?php

namespace Illuminate\Database\Query;

if (class_exists('Illuminate\Database\Query\Builder')) {
    return;
}

class Builder
{
    public function publicMethodBelongsToQueryBuilder()
    {
    }

    protected function protectedMethodBelongsToQueryBuilder()
    {
    }

    private function privateMethodBelongsToQueryBuilder()
    {
    }
}
