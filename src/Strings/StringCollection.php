<?php

namespace Adapt\Foundation\Strings;

use Adapt\Foundation\Arrays\AsArray;
use Adapt\Foundation\Arrays\ToArray;
use Adapt\Foundation\Collections\Collection;

class StringCollection extends Collection implements ToString
{
    public function __construct(ToArray|AsArray|array $array = [])
    {
        parent::__construct($array);
        $this->transform(function ($value) {
            if (is_string($value)) {
                return Str::fromString($value);
            }

            if ($value instanceof Str) {
                return $value;
            }

            if ($value instanceof ToString) {
                return Str::fromString($value->toString());
            }

            return null;
        });
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {
        if (is_string($value)) {
            $value = Str::fromString($value);
        }
        parent::offsetSet($offset, $value);
    }

    public function toString(): string
    {
        return $this->implode('')->toString();
    }

    public function __toString(): string
    {
        return $this->toString();
    }
}
