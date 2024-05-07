<?php

namespace RectorLaravel\Util;

use PHPStan\Type\ObjectType;
use Rector\TypeDeclaration\ValueObject\AddParamTypeForFunctionLikeWithinCallLikeArgDeclaration;

class AddParamTypeForFunctionLikeWithinCallLikeArgDeclarationRectorConfigGenerator
{
    /**
     * @param array<int, string|int<0, max>> $callPositionsOrNames
     * @param class-string[] $targetClasses
     * @param int<0, max> $functionPosition
     * @param string $methodName
     * @param ObjectType $type
     * @return AddParamTypeForFunctionLikeWithinCallLikeArgDeclaration[]
     */
    public function generate(
        array $callPositionsOrNames,
        array $targetClasses,
        int $functionPosition,
        string $methodName,
        ObjectType $type
    ): array {
        $configurations = [];

        foreach ($callPositionsOrNames as $callPositionsOrName) {
            foreach ($targetClasses as $targetClass) {
                $configurations[] = new AddParamTypeForFunctionLikeWithinCallLikeArgDeclaration(
                    $targetClass,
                    $methodName,
                    $callPositionsOrName,
                    $functionPosition,
                    $type
                );
            }
        }

        return $configurations;
    }
}
