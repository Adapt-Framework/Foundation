<?php

namespace Adapt\Foundation\Collections;

use Adapt\Foundation\Arrays\Arr;
use Adapt\Foundation\Arrays\AsArray;
use Adapt\Foundation\Arrays\ToArray;
use Adapt\Foundation\Comparison\Compare;
use Adapt\Foundation\Strings\Str;
use Adapt\Foundation\Strings\ToString;
use Closure;

class Collection extends Arr
{
    public function all(): array
    {
        return $this->asArray();
    }

    public function average(ToString|string $key): float|int
    {
        if (!$this->count()) {
            return 0;
        }

        if ($key instanceof ToString) {
            $key = $key->toString();
        }
        return $this->column($key)->filter()->sum() / $this->count();
    }

    public function chunkWhile(Closure $closure): static
    {
        $chunks = Collection::fromArray([]);
        $chunk = Collection::fromArray([]);
        foreach ($this->items as $key => $value) {
            if (count($chunk) === 0) {
                $chunk[$key] = $value;
                continue;
            }

            if ($closure($value, $key, $chunk)) {
                $chunk[$key] = $value;
            } else {
                $chunks[] = $chunk;
                $chunk = static::fromArray([$key => $value]);
            }
        }

        if ($chunk->count()) {
            $chunks[] = $chunk;
        }

        return $chunks;
    }

    public function collapse(): static
    {
        $output = static::fromArray([]);
        foreach ($this->items as $item) {
            if ($item instanceof AsArray) {
                $item = $item->asArray();
            }

            if (is_array($item)) {
                $output = $output->merge($item);
            } else {
                $output[] = $item;
            }
        }

        return $output;
    }

    public function collect(): static
    {
        return static::fromArray($this->items);
    }

    public function contains(mixed $item): bool
    {
        if ($item instanceof Closure) {
            return (bool)$this->filter($item)->count();
        }

        return $this->in($item);
    }

    public function countBy(Closure|null $closure = null): static
    {
        $output = static::fromArray([]);
        array_walk(
            $this->items,
            function ($value, $key) use ($closure, $output) {
                if ($closure) {
                    $value = $closure($value, $key);
                }

                if (isset($output[$value])) {
                    $output[$value] = $output[$value] + 1;
                } else {
                    $output[$value] = 1;
                }
            }
        );

        return $output;
    }

    public function crossJoin(ToArray|array ...$arrays): static
    {
        $output = static::fromArray([]);
        foreach ($this->items as $key => $value) {
            $item = [$value];
            foreach ($arrays as $array) {
                foreach ($array as $a) {
                    $item = array_merge($item, [$a]);
                }
            }
            $output[] = static::fromArray($item);
        }
        return $output;
    }

    public function doesntContain(mixed $item): bool
    {
        return !$this->contains($item);
    }

    public function duplicates(ToString|string|null $key = null): static
    {
        $closure = function($value){ return $value > 1; };
        if (!$key) {
            return $this->countBy()->filter($closure)->flip();
        }

        if ($key instanceof ToString) {
            $key = $key->toString();
        }

        return $this->column($key)->countBy()->filter($closure)->flip();
    }

    public function each(Closure $closure): static
    {
        array_walk($this->items, $closure);
        return $this;
    }

    public function eachSpread(Closure $closure): static
    {
        return $this->each(
            function ($item) use ($closure) {
                if ($item instanceof AsArray) {
                    $item = $item->asArray();
                }

                if (is_array($item)) {
                    $closure(...$item);
                } else {
                    $closure($item);
                }
            }
        );
    }

    public function every(Closure $closure): bool
    {
        return $this->count() === $this->filter($closure)->count();
    }

    public function except(AsArray|array $keys): static
    {
        if ($keys instanceof AsArray) {
            $keys = $keys->asArray();
        }
        return $this->filter(
            function($value, $key) use ($keys) {
                return !in_array($key, $keys);
            }
        );
    }

    public function firstWhere(ToString|string $key, ToString|string|null $valueOrOperator = null, mixed $value = null): static
    {
        if ($key instanceof ToString) {
            $key = $key->toString();
        }

        if ($valueOrOperator instanceof ToString) {
            $valueOrOperator = $valueOrOperator->toString();
        }

        if ($value instanceof ToString) {
            $value = $value->toString();
        }

        foreach($this->items as $item) {
            if (!$item instanceof ToArray && !is_array($item)) {
                continue;
            }

            if (!isset($item[$key])) {
                continue;
            }

            if (!$valueOrOperator) {
                return static::fromArray($item);
            }

            if (!$value) {
                if (Compare::test($item[$key], Compare::EQUALS, $value)) {
                    return static::fromArray($item);
                }
                continue;
            }

            if (Compare::test($item[$key], $valueOrOperator, $value)) {
                return static::fromArray($item);
            }
        }

        return static::fromArray([]);
    }

    public function flatMap(Closure $closure): static
    {
        // @todo
    }

    public function flatten(int|null $depth = null): static
    {
        // @todo
    }

    public function forget(ToString|string $key): static
    {
        if ($key instanceof ToString) {
            $key = $key->toString();
        }
        unset($this->items[$key]);
        return $this;
    }

    public function groupBy(Closure|ToString|string $grouping): static
    {
        if ($grouping instanceof ToString) {
            $grouping = $grouping->toString();
        }

        $output = static::create();
        foreach($this->items as $item) {
            if (!is_array($item) && !$item instanceof Arr) {
                continue;
            }

            foreach($item as $key => $value) {
                $groupValue = is_string($grouping) ? $grouping : $grouping($value, $key);
                if (!isset($output[$groupValue])) {
                    $output[$groupValue] = static::create();
                }
                $output[$groupValue][] = $item;
            }
        }

        return $output;
    }

    public function has(ToString|string $key): bool
    {
        if ($key instanceof ToString) {
            $key = $key->toString();
        }

        return $this->keys()->in($key);
    }

    public function implode(ToString|string $keyOrGlue, ToString|string|null $glue = null): Str
    {
        if ($keyOrGlue instanceof ToString) {
            $keyOrGlue = $keyOrGlue->toString();
        }

        if ($glue instanceof ToString) {
            $glue = $glue->toString();
        }

        if ($glue) {
            return $this->column($keyOrGlue)->implode($glue);
        }

        return Str::fromString(implode($glue, $this->items));
    }

    public function isEmpty(): bool
    {
        return (bool)$this->count();
    }

    public function isNotEmpty(): bool
    {
        return !$this->isEmpty();
    }

    public function join(ToString|string $glue, ToString|string|null $final = null): Str
    {
        if (!$final) {
            return $this->implode($glue);
        }

        if ($final instanceof ToString) {
            $final = $final->toString();
        }

        return match ($this->count()) {
            0 => Str::fromString(''),
            1 => Str::fromString($this->first()),
            2 => $this->implode($final),
            default => static::fromArray([
                    $this->slice(0, $this->count() - 1)->implode($glue)->toString(),
                    $this->last()
                ])->implode($final)
        };
    }

    public function keyBy(Closure|ToString|string $key): static
    {
        if ($key instanceof ToString) {
            $key = $key->toString();
        }

        $output = static::create();
        foreach($this->items as $item) {
            if (!is_array($item) && !$item instanceof ToArray) {
                continue;
            }

            $keyName = is_string($key) ? $this->items[$key] : $key($item);
            $output[$keyName] = $item;
        }

        return $output;
    }

    public static function make(Closure $closure): static
    {
        return static::fromArray($closure());
    }

    public function mapInto(ToString|string $class, mixed ...$additionalConstructorParams): static
    {
        if ($class instanceof ToString) {
            $class = $class->toString();
        }
        return $this->map(function($value) use ($class, $additionalConstructorParams) {
            if (count($additionalConstructorParams)) {
                return new $class($value, ...$additionalConstructorParams);
            }

            return new $class($value);
        });
    }

    public function mapSpread(Closure $closure): static
    {
        return $this->map(function($item) use ($closure) {
            if (is_array($item)) {
                return $closure(...$item);
            }

            return $closure($item);
        });
    }


}
