<?php

namespace RectorLaravel\Util;

use PHPStan\Type\ObjectType;
use Rector\TypeDeclaration\ValueObject\AddParamTypeForFunctionLikeWithinCallLikeArgDeclaration;

class AddParamTypeForFunctionLikeWithinCallLikeArgDeclarationRectorConfigGenerator
{
    /**
     * @param  array<int, string|int<0, max>>  $callPositionsOrNames
     * @param  class-string[]  $targetClasses
     * @param  int<0, max>  $functionPosition
     * @return AddParamTypeForFunctionLikeWithinCallLikeArgDeclaration[]
     */
    public function generate(
        array $callPositionsOrNames,
        array $targetClasses,
        int $functionPosition,
        string $methodName,
        ObjectType $objectType
    ): array {
        $configurations = [];

        foreach ($callPositionsOrNames as $callPositionOrName) {
            foreach ($targetClasses as $targetClass) {
                $configurations[] = new AddParamTypeForFunctionLikeWithinCallLikeArgDeclaration(
                    $targetClass,
                    $methodName,
                    $callPositionOrName,
                    $functionPosition,
                    $objectType
                );
            }
        }

        return $configurations;
    }
}
