<?php

namespace Adapt\Foundation\Json;

use Adapt\Foundation\Arrays\AsArray;
use Adapt\Foundation\Arrays\ToArray;
use Adapt\Foundation\Collections\Collection;
use Adapt\Foundation\Strings\FromString;
use Adapt\Foundation\Strings\Str;
use Adapt\Foundation\Strings\ToString;

class Json extends Collection implements ToString, FromString
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

            if ($value instanceof ToArray || $value instanceof AsArray || is_array($value)) {
                return Json::fromArray($value);
            }

            return $value;
        });
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {
        if (is_string($value)) {
            $value = Str::fromString($value);
        } elseif ($value instanceof AsArray || $value instanceof ToArray || is_array($value)) {
            $value = Json::fromArray($value);
        }

        parent::offsetSet($offset, $value);
    }

    public function toString(): string
    {
        return json_encode($this->toArray());
    }

    public static function fromString(string $string): static
    {
        return static::fromArray(json_decode($string, true) ?? []);
    }
}