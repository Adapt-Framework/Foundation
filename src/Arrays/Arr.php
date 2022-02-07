<?php

namespace Adapt\Foundation\Arrays;

use Closure;
use JetBrains\PhpStorm\Pure;

class Arr extends Foundation
{
    public function isAssoc(): bool
    {
        return array_keys($this->items) !== range(0, count($this->items) - 1);
    }

    public function keys(): static
    {
        return static::fromArray(array_keys($this->items));
    }

     public function upperCaseKeys(): static
     {
         return static::fromArray(array_change_key_case($this->items, CASE_UPPER));
     }

     public function lowerCaseKeys(): static
     {
         return static::fromArray(array_change_key_case($this->items, CASE_LOWER));
     }

     public function chunk(int $length, bool $preserveKeys = false): static
     {
         return static::fromArray(
             array_map(
                 function($group) {
                     return static::fromArray($group);
                 },
                 array_chunk($this->items, $length, $preserveKeys)
             )
         );
     }

     public function column(int|string|null $columnKey, int|string|null $indexKey = null): static
     {
         return static::fromArray(array_column($this->items, $columnKey, $indexKey));
     }

    public function combineWithKeys(array|AsArray $keys): static
    {
        if ($keys instanceof AsArray) {
            $keys = $keys->asArray();
        }
        return static::fromArray(array_combine($keys, $this->items));
    }

    public function combineWithValues(array|AsArray $values): static
    {
        if ($values instanceof AsArray) {
            $values = $values->asArray();
        }
        return static::fromArray(array_combine($this->items, $values));
    }

    public function countValues(): static
    {
        return static::fromArray(array_count_values($this->items));
    }

    public function diffAssoc(AsArray|array ...$arrays): static
    {
        array_walk(
            $arrays,
            function(&$value, $key) {
                if ($value instanceof AsArray) {
                    $value = $value->asArray();
                }
            }
        );

        return static::fromArray(array_diff_assoc($this->items, ...$arrays));
    }

    public function diffKey(AsArray|array ...$arrays): static
    {
        array_walk(
            $arrays,
            function(&$value, $key) {
                if ($value instanceof AsArray) {
                    $value = $value->asArray();
                }
            }
        );

        return static::fromArray(array_diff_key($this->items, ...$arrays));
    }

    public function diff(AsArray|array ...$arrays): static
    {
        array_walk(
            $arrays,
            function(&$value, $key) {
                if ($value instanceof AsArray) {
                    $value = $value->asArray();
                }
            }
        );

        return static::fromArray(array_diff($this->items, ...$arrays));
    }

    public function fillKeys(mixed $value): static
    {
        return static::fromArray(array_fill_keys($this->items, $value));
    }

    public static function fill(int $count, mixed $value, int $startIndex = 0): static
    {
        return static::fromArray(array_fill($startIndex, $count, $value));
    }

    public function filter(?Closure $closure = null): static
    {
        return static::fromArray(array_filter($this->items, $closure, ARRAY_FILTER_USE_BOTH));
    }

    public function flip(): static
    {
        return static::fromArray(array_flip($this->items));
    }

    public function intersectAssoc(AsArray|array ...$arrays): static
    {
        array_walk(
            $arrays,
            function(&$value, $key) {
                if ($value instanceof AsArray) {
                    $value = $value->asArray();
                }
            }
        );

        return static::fromArray(array_intersect_assoc($this->items, ...$arrays));
    }

    public function intersectKey(AsArray|array ...$arrays): static
    {
        array_walk(
            $arrays,
            function(&$value, $key) {
                if ($value instanceof AsArray) {
                    $value = $value->asArray();
                }
            }
        );

        return static::fromArray(array_intersect_key($this->items, ...$arrays));
    }

    public function intersect(AsArray|array ...$arrays): static
    {
        array_walk(
            $arrays,
            function(&$value, $key) {
                if ($value instanceof AsArray) {
                    $value = $value->asArray();
                }
            }
        );

        return static::fromArray(array_intersect($this->items, ...$arrays));
    }

    public function isList(): bool
    {
        $keys = $this->keys()->toArray();
        for($i = 0; $i < $this->count(); $i++) {
            if ($keys[$i] !== $i) {
                return false;
            }
        }

        return true;
    }

    public function first(): mixed
    {
        if ($this->count()) {
            if ($this->isAssoc()) {
                return $this->items[array_keys($this->items)[0]];
            }

            return $this->items[0];
        }

        return null;
    }

    public function last(): mixed
    {
        if ($this->count()) {
            if ($this->isAssoc()) {
                return $this->items[array_keys($this->items)[$this->count() - 1]];
            }

            return $this->items[$this->count() - 1];
        }

        return null;
    }

    public function keyExists(string|int $key): bool
    {
        return array_key_exists($key, $this->items);
    }

    public function keyFirst(): int|string|null
    {
        return array_key_first($this->items);
    }

    public function keyLast(): int|string|null
    {
        return array_key_last($this->items);
    }

    public function map(Closure $closure): static
    {
        return static::fromArray(array_map($closure, $this->items));
    }

    public function merge(array ...$arrays): static
    {
        return static::fromArray(array_merge($this->items, ...$arrays));
    }

    public function pad(int $length, mixed $value): static
    {
        return static::fromArray(array_pad($this->items, $length, $value));
    }

    public function pop(): mixed
    {
        $this->index = 0;
        return array_pop($this->items);
    }

    public function product(): int|float
    {
        return array_product($this->items);
    }

    public function push(mixed ...$values): int
    {
        return array_push($this->items, ...$values);
    }

    public function randKey(int $number = 1): static|string|int
    {
        if ($number === 1) {
            return array_rand($this->items);
        }

        return static::fromArray(array_rand($this->items, $number));
    }

    public function rand(int $number = 1): mixed
    {
        $keys = $this->randKey($number);
        if ($number === 1) {
            return $this->items[$keys];
        }
        $self = $this;
        return $keys->map(function($key) use ($self) {
            return $self[$key];
        });
    }

    public function reduce(Closure $closure, mixed $initial = null): mixed
    {
        return array_reduce($this->items, $closure, $initial);
    }

    public function replace(AsArray|array ...$replacements): static
    {
        array_walk(
            $replacements,
            function(&$value, $key) {
                if ($value instanceof AsArray) {
                    $value = $value->asArray();
                }
            }
        );

        return static::fromArray(array_replace($this->items, ...$replacements));
    }

    public function reverse(): static
    {
        return static::fromArray(array_reverse($this->items));
    }

    public function search(mixed $needle, bool $strict = false): int|string|false
    {
        return array_search($needle, $this->items, $strict);
    }

    public function shift(): mixed
    {
        return array_shift($this->items);
    }

    public function slice(int $offset, ?int $length = null, bool $preserveKeys = false): static
    {
        return static::fromArray(array_slice($this->items, $offset, $length, $preserveKeys));
    }

    public function splice(int $offset, ?int $length, mixed $replacement = []): static
    {
        return static::fromArray(array_splice($this->items, $offset, $length, $replacement));
    }

    public function sum(): float|int
    {
        return array_sum($this->items);
    }

    public function unique(int $sort = SORT_STRING): static
    {
        return static::fromArray(array_unique($this->items, $sort));
    }
}