<?php

namespace Illuminate\Database\Query;

if (class_exists('Illuminate\Database\Query\Builder')) {
    return;
}

class Builder implements \Illuminate\Contracts\Database\Query\Builder
{
    public function publicMethodBelongsToQueryBuilder(): void {}

    protected function protectedMethodBelongsToQueryBuilder(): void {}

    private function privateMethodBelongsToQueryBuilder(): void {}
}
