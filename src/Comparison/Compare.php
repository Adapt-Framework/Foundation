<?php

namespace Adapt\Foundation\Comparison;

use Adapt\Foundation\Strings\ToString;

class Compare
{
    public const EQUALS = '==';
    public const EQUALS_AND_SAME_TYPE = '===';
    public const GREATER_THAN = '>';
    public const GREATER_THAN_OR_EQUALS = '>=';
    public const LESS_THAN = '<';
    public const LESS_THAN_OR_EQUALS = '<=';
    public const NOT_EQUALS = '!=';
    public const NOT_EQUALS_OR_NOT_SAME_TYPE = '!==';

    public static function test(mixed $value1, ToString|string $operator, mixed $value2): bool
    {
        if ($operator instanceof ToString) {
            $operator = $operator->toString();
        }
        return match ($operator) {
            static::EQUALS => $value1 == $value2,
            static::EQUALS_AND_SAME_TYPE => $value1 === $value2,
            static::GREATER_THAN => $value1 > $value2,
            static::GREATER_THAN_OR_EQUALS => $value1 >= $value2,
            static::LESS_THAN => $value1 < $value2,
            static::LESS_THAN_OR_EQUALS => $value1 <= $value2,
            static::NOT_EQUALS => $value1 != $value2,
            static::NOT_EQUALS_OR_NOT_SAME_TYPE => $value1 !== $value2,
            default => false,
        };

    }
}
