<?php

namespace Adapt\Foundation\Arrays;

use ArrayAccess;
use Countable;
use OutOfBoundsException;
use SeekableIterator;
use Serializable;

abstract class Foundation implements ArrayAccess, Countable, SeekableIterator, Serializable, ToArray, FromArray, AsArray
{
    protected array $items;
    protected mixed $index = 0;

    public function __construct(AsArray|array $array = [])
    {
        if ($array instanceof AsArray) {
            $array = $array->asArray();
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

    public function seek(int $offset): void
    {
        if (!isset($this->items[$offset])) {
            throw new OutOfBoundsException(sprintf('Invalid index: %s', $offset));
        }

        $this->index = $offset;
    }

    public function current(): mixed
    {
        return $this->items[$this->index];
    }

    public function next(): void
    {
        ++$this->index;
    }

    public function key(): mixed
    {
        return $this->index;
    }

    public function valid(): bool
    {
        return isset($this->items[$this->index]);
    }

    public function rewind()
    {
        $this->index = 0;// OR = array_keys($this->items)[0]...
    }

    public function serialize(): string
    {
        return serialize(['items' => $this->items, 'index' => $this->index]);
    }

    public function unserialize(string $data): void
    {
        $values = unserialize($data);
        if (isset($values['index'])) {
            $this->index = $values['index'];
        }
        if (isset($values['items'])) {
            $this->items = $values['items'];
        }
    }

    public static function fromArray(ToArray|array $array): static
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
