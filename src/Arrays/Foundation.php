<?php

namespace Adapt\Foundation\Arrays;

use Adapt\Foundation\Arrays\Contracts\AsArray;
use Adapt\Foundation\Arrays\Contracts\FromArray;
use Adapt\Foundation\Arrays\Contracts\ToArray;
use ArrayAccess;
use Countable;
use Iterator;
use Serializable;

abstract class Foundation implements ArrayAccess, Countable, Iterator, Serializable, ToArray, FromArray,
    AsArray
{
    protected array $items;
    protected mixed $index = 0;

    public function __construct(ToArray|AsArray|array $array = [])
    {
        if ($array instanceof AsArray) {
            $array = $array->asArray();
        }

        if ($array instanceof ToArray) {
            $array = $array->toArray();
        }

        $this->items = $array;
    }

    public function offsetExists(mixed $offset): bool
    {
        return isset($this->items[$offset]);
    }

    public function offsetGet(mixed $offset): mixed
    {
        return $this->items[$offset] ?? null;
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {
        if (is_null($offset)) {
            $this->items[] = $value;
        } else {
            $this->items[$offset] = $value;
        }
    }

    public function offsetUnset(mixed $offset): void
    {
        unset($this->items[$offset]);
    }

    public function count(): int
    {
        return count($this->items);
    }

    public function current(): mixed
    {
        return current($this->items);
    }

    public function next(): void
    {
        next($this->items);
    }

    public function key(): string|int|null
    {
        return key($this->items);
    }

    public function valid(): bool
    {
        return key($this->items) !== null;
    }

    public function rewind(): void
    {
        reset($this->items);
    }

    public function serialize(): string
    {
        return serialize($this->__serialize());
    }

    public function __unserialize(array $data): void
    {
        if (isset($data['index'])) {
            $this->index = $data['index'];
        }
        if (isset($data['items'])) {
            $this->items = $data['items'];
        }
    }

    public function unserialize(string $data): void
    {
        $values = unserialize($data);
        $this->__unserialize($values);
    }

    public function __serialize(): array
    {
        return ['items' => $this->items, 'index' => $this->index];
    }

    public static function fromArray(ToArray|AsArray|array $array): static
    {
        return new static($array);
    }

    public function toArray(): array
    {
        $output = $this->items;
        array_walk(
            $output,
            function (&$value, $key) {
                if ($value instanceof ToArray) {
                    $value = $value->toArray();
                }elseif ($value instanceof AsArray) {
                    $value = $value->asArray();
                }
            }
        );

        return $output;
    }

    public function asArray(): array
    {
        return $this->items;
    }
}
