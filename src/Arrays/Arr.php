<?php

namespace Adapt\Foundation\Arrays;

use Adapt\Foundation\Arrays\Contracts\AsArray;
use Adapt\Foundation\Extending\ExtendableTrait;
use Adapt\Foundation\Strings\Contracts\ToString;
use Closure;

class Arr extends Foundation
{
    use ExtendableTrait;

    /**
     * Return an empty Arr
     *
     * @return static
     */
    public static function create(): static
    {
        return new static([]);
    }

    /**
     * Is this an associative array?
     *
     * @return bool
     */
    public function isAssoc(): bool
    {
        return array_keys($this->items) !== range(0, count($this->items) - 1);
    }

    /**
     * Returns the array keys
     *
     * @return $this
     */
    public function keys(): static
    {
        return static::fromArray(array_keys($this->items));
    }

    /**
     * Returns the array keys in uppercase.
     *
     * @return $this
     */
     public function upperCaseKeys(): static
     {
         return static::fromArray(array_change_key_case($this->items, CASE_UPPER));
     }

    /**
     * Returns the array keys in lowercase
     *
     * @return $this
     */
     public function lowerCaseKeys(): static
     {
         return static::fromArray(array_change_key_case($this->items, CASE_LOWER));
     }

    /**
     * Returns an array of arrays of the given chunk size.
     *
     * @param int $length
     * @param bool $preserveKeys
     * @return $this
     */
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

    /**
     * Return the column of the array, optionally named with $indexKey
     *
     * @param int|string|null $columnKey
     * @param int|string|null $indexKey
     * @return $this
     */
     public function column(int|string|null $columnKey, int|string|null $indexKey = null): static
     {
         return static::fromArray(array_column($this->items, $columnKey, $indexKey));
     }

    /**
     * Combines the values of this array with the given keys
     *
     * @param array|AsArray $keys
     * @return $this
     */
    public function combineWithKeys(array|AsArray $keys): static
    {
        if ($keys instanceof AsArray) {
            $keys = $keys->asArray();
        }
        return static::fromArray(array_combine($keys, $this->items));
    }

    /**
     * Uses the values of this array as keys for the values of the given array.
     *
     * @param array|AsArray $values
     * @return $this
     */
    public function combineWithValues(array|AsArray $values): static
    {
        if ($values instanceof AsArray) {
            $values = $values->asArray();
        }
        return static::fromArray(array_combine($this->items, $values));
    }

    /**
     * Returns a count of the values in this array
     *
     * @return $this
     */
    public function countValues(): static
    {
        return static::fromArray(array_count_values($this->items));
    }

    /**
     * Returns turns the difference between this array and the given associative array.
     *
     * @param AsArray|array ...$arrays
     * @return $this
     */
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

    /**
     * Returns the difference in keys between this array and the given array.
     *
     * @param AsArray|array ...$arrays
     * @return $this
     */
    public function diffKeys(AsArray|array ...$arrays): static
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

    /**
     * Returns the difference between this array and the given array.
     *
     * @param AsArray|array ...$arrays
     * @return $this
     */
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

    /**
     * Using the values of this array as the keys to the values supplied, returning
     * both together.
     *
     * @param mixed $value
     * @return $this
     */
    public function fillKeys(mixed $value): static
    {
        return static::fromArray(array_fill_keys($this->items, $value));
    }

    /**
     * Fills this array with values
     *
     * @param int $count
     * @param mixed $value
     * @param int $startIndex
     * @return static
     *
     */
    public static function fill(int $count, mixed $value, int $startIndex = 0): static
    {
        return static::fromArray(array_fill($startIndex, $count, $value));
    }

    /**
     * Applies the $closure to each element in this array and uses the returned boolean to
     * filter out elements from the returned array.
     *
     * @param Closure|null $closure
     * @return $this
     */
    public function filter(?Closure $closure = null): static
    {
        return static::fromArray(array_filter($this->items, $closure, ARRAY_FILTER_USE_BOTH));
    }

    /**
     * Flips the keys and values of this array around.
     *
     * @return $this
     */
    public function flip(): static
    {
        return static::fromArray(array_flip($this->items));
    }

    /**
     * Gets a value from the array using the $key as a dot notation path
     *
     * @param ToString|string $key
     * @return mixed
     */
    public function get(ToString|string $key): mixed
    {
        return ArrayPath::fromString($key)->extractFromArray($this->items);
    }

    /**
     * Intersects an associative array
     *
     * @param AsArray|array ...$arrays
     * @return $this
     */
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

    /**
     * Intersection of this array and the provided array using the array keys
     *
     * @param AsArray|array ...$arrays
     * @return $this
     */
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

    /**
     * Returns the intersection of this array and the provided array
     *
     * @param AsArray|array ...$arrays
     * @return $this
     */
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

    /**
     * Is this array a list?
     *
     * @return bool
     */
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

    /**
     * Returns the first element in the array
     *
     * @return mixed
     */
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

    /**
     * Returns the last element in the array
     *
     * @return mixed
     */
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

    /**
     * Checks if the key exists in this array
     *
     * @param string|int $key
     * @return bool
     */
    public function keyExists(string|int $key): bool
    {
        return array_key_exists($key, $this->items);
    }

    /**
     * Get the first key in this array
     *
     * @return int|string|null
     */
    public function keyFirst(): int|string|null
    {
        return array_key_first($this->items);
    }

    /**
     * Returns the last key in this array
     *
     * @return int|string|null
     */
    public function keyLast(): int|string|null
    {
        return array_key_last($this->items);
    }

    /**
     * Maps the elements in this array against the given closure
     *
     * @param Closure $closure
     * @return $this
     */
    public function map(Closure $closure): static
    {
        return static::fromArray(array_map($closure, $this->items));
    }

    /**
     * Merges this array with the provided array(s)
     *
     * @param AsArray|array ...$arrays
     * @return $this
     */
    public function merge(AsArray|array ...$arrays): static
    {
        array_walk(
            $arrays,
            function(&$value, $key) {
                if ($value instanceof AsArray) {
                    $value = $value->asArray();
                }
            }
        );

        return static::fromArray(array_merge($this->items, ...$arrays));
    }

    /**
     * Merges this array recursively with the provided array(s)
     *
     * @param AsArray|array ...$arrays
     * @return $this
     */
    public function mergeRecursive(AsArray|array ...$arrays): static
    {
        array_walk(
            $arrays,
            function(&$value, $key) {
                if ($value instanceof AsArray) {
                    $value = $value->asArray();
                }
            }
        );

        return static::fromArray(array_merge_recursive($this->items, ...$arrays));
    }

    /**
     * Pads this array with values
     *
     * @param int $length
     * @param mixed $value
     * @return $this
     */
    public function pad(int $length, mixed $value): static
    {
        return static::fromArray(array_pad($this->items, $length, $value));
    }

    /**
     * Pops the last element off this array and returns it
     *
     * @return mixed
     */
    public function pop(): mixed
    {
        $this->index = 0;
        return array_pop($this->items);
    }

    /**
     * Returns the product of this array
     *
     * @return int|float
     */
    public function product(): int|float
    {
        return array_product($this->items);
    }

    /**
     * Pushes a value on to the end of this array
     * @param mixed ...$values
     * @return int
     */
    public function push(mixed ...$values): int
    {
        return array_push($this->items, ...$values);
    }

    /**
     * Returns one or more random keys from this array
     *
     * @param int $number
     * @return string|int|$this
     */
    public function randKey(int $number = 1): static|string|int
    {
        if ($number === 1) {
            return array_rand($this->items);
        }

        return static::fromArray(array_rand($this->items, $number));
    }

    /**
     * Returns one or more random elements from this array
     *
     * @param int $number
     * @return mixed
     */
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

    /**
     * Reduces this array using the given closure
     *
     * @param Closure $closure
     * @param mixed|null $initial
     * @return mixed
     */
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

    public function replaceRecursive(AsArray|array ...$replacements): static
    {
        array_walk(
            $replacements,
            function(&$value, $key) {
                if ($value instanceof AsArray) {
                    $value = $value->asArray();
                }
            }
        );

        return static::fromArray(array_replace_recursive($this->items, ...$replacements));
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
        array_splice($this->items, $offset, $length, $replacement);
        return static::fromArray($this->items);
    }

    public function sum(): float|int
    {
        return array_sum($this->items);
    }

    public function unique(int $sort = SORT_STRING): static
    {
        return static::fromArray(array_unique($this->items, $sort));
    }

    public function unshift(mixed ...$values): int
    {
        return array_unshift($this->items, ...$values);
    }

    public function values(): static
    {
        return static::fromArray(array_values($this->items));
    }

    public function walk(Closure $closure, mixed $arg = null): bool
    {
        return array_walk($this->items, $closure, $arg);
    }

    public function sortAscending(bool $preserveKeys = false, int $flags = SORT_REGULAR): static
    {
        $items = $this->items;

        if ($preserveKeys) {
            asort($items, $flags);
        } else {
            sort($items, $flags);
        }

        return static::fromArray($items);
    }

    public function sortDescending(bool $preserveKeys = false, int $flags = SORT_REGULAR): static
    {
        $items = $this->items;
        if ($preserveKeys) {
            arsort($items, $flags);
        } else {
            rsort($items, $flags);
        }

        return static::fromArray($items);
    }

    public function in(mixed $value): bool
    {
        return in_array($value, $this->items);
    }

    public function sortKeysAscending(int $flags = SORT_REGULAR): static
    {
        $items = $this->items;
        ksort($items, $flags);
        return static::fromArray($items);
    }

    public function sortKeysDescending(int $flags = SORT_REGULAR): static
    {
        $items = $this->items;
        krsort($items, $flags);
        return static::fromArray($items);
    }

    public function sortNaturally(bool $caseSensitive = false): static
    {
        $items = $this->items;
        if ($caseSensitive) {
            natcasesort($items);
        } else {
            natsort($items);
        }

        return static::fromArray($items);
    }

    public static function range(string|int|float $start, string|int|float $end, int|float $step = 1): static
    {
        return static::fromArray(range($start, $end, $start));
    }

    public function shuffle(): bool
    {
        return shuffle($this->items);
    }
}
