<?php

namespace Adapt\Foundation\Json;

use Adapt\Foundation\Arrays\Contracts\AsArray;
use Adapt\Foundation\Arrays\Contracts\ToArray;
use Adapt\Foundation\Collections\Collection;
use Adapt\Foundation\Strings\Contracts\FromString;
use Adapt\Foundation\Strings\Contracts\ToString;
use Adapt\Foundation\Strings\Str;

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

    public function toArray(): array
    {
        $output = $this->items;
        array_walk(
            $output,
            function (&$value, $key) {
                if ($value instanceof ToArray) {
                    $value = $value->toArray();
                } elseif ($value instanceof AsArray) {
                    $value = $value->asArray();
                } elseif ($value instanceof ToString) {
                    $value = $value->toString();
                }
            }
        );

        return $output;
    }

    public function __toString(): string
    {
        return $this->toString();
    }


}
