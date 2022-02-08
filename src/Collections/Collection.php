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
        $closure = function ($value) {
            return $value > 1;
        };
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
            function ($value, $key) use ($keys) {
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

        foreach ($this->items as $item) {
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
        foreach ($this->items as $item) {
            if (!is_array($item) && !$item instanceof Arr) {
                continue;
            }

            foreach ($item as $key => $value) {
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
        foreach ($this->items as $item) {
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
        return $this->map(function ($value) use ($class, $additionalConstructorParams) {
            if (count($additionalConstructorParams)) {
                return new $class($value, ...$additionalConstructorParams);
            }

            return new $class($value);
        });
    }

    public function mapSpread(Closure $closure): static
    {
        return $this->map(function ($item) use ($closure) {
            if (is_array($item)) {
                return $closure(...$item);
            }

            return $closure($item);
        });
    }

    public function mapToGroup(Closure $closure): static
    {
        $output = static::create();
        foreach ($this->items as $key => $value) {
            $output = $output->merge($closure($value, $key));
        }

        return $output;
    }

    public function mapWithKeys(Closure $closure): static
    {
        // @todo
    }

    public function max(ToString|string|null $key = null): mixed
    {
        if (!$key) {
            return max($this->items);
        }

        return max($this->column($key)->toArray());
    }

    public function median(ToString|string|null $key = null): mixed
    {
        $values = null;
        if ($key) {
            $values = $this->column($key);
        } else {
            $values = $this->collect();
        }

        $values->sortAscending();

        if (!$values->count()) {
            return null;
        }

        $midIndex = floor(($values->count() - 1) / 2);
        if ($values->count() % 2 || $values->count() < 2) {
            return $values[$midIndex];
        }

        $lowerValue = $values[$midIndex];
        $upperValue = $values[$midIndex + 1];
        return (($lowerValue + $upperValue) / 2);
    }

    public function mergeRecursive(AsArray|array ...$arrays): static
    {
        // @todo... (Move to Arr?)
    }

    public function min(ToString|string|null $key = null): mixed
    {
        if (!$key) {
            return min($this->items);
        }

        return min($this->column($key)->toArray());
    }

    public function mode(ToString|string|null $key = null): mixed
    {
        // @todo
    }

    public function nth(int $every, int|null $offset = null): static
    {
        // @todo
    }

    public function only(AsArray|array $keys): static
    {
        if ($keys instanceof AsArray) {
            $keys = $keys->asArray();
        }
        return $this->filter(
            function ($value, $key) use ($keys) {
                return in_array($key, $keys);
            }
        );
    }

    public function partition(Closure $closure): static
    {
        $groups = static::fromArray([
            static::create(),
            static::create()
        ]);

        $this->each(function ($item) use ($closure, &$groups) {
            if ($closure($item)) {
                $groups[0][] = $item;
            } else {
                $groups[1][] = $item;
            }
        });

        return $groups;
    }

    public function pipe(Closure $closure): mixed
    {
        return $closure($this);
    }

    public function pipeInto(ToString|string $class): mixed
    {
        if ($class instanceof ToString) {
            $class = $class->toString();
        }
        return new $class($this);
    }

    /**
     * @param Closure[] $closures
     * @return mixed
     */
    public function pipeThrough(AsArray|array $closures): mixed
    {
        if ($closures instanceof AsArray) {
            $closures = $closures->asArray();
        }

        $output = $this;
        foreach ($closures as $closure) {
            if (!$closure instanceof Closure) {
                continue;
            }
            $output = $closure($output);
        }

        return $output;
    }

    public function pluck(ToString|string $key, ToString|string|null $keyBy = null): static
    {
        if (!$key instanceof ToString) {
            $key = Str::fromString($key);
        }

        if ($keyBy instanceof ToString) {
            $keyBy = $keyBy->toString();
        }

        if (!$key->contains('.')) {
            return $this->column($key->toString(), $keyBy);
        }

        $output = $this;
        /** @var Str $keyPart */
        foreach($key as $keyPart) {
            $output = $output->column($keyPart->toString());
        }

        return $output;
    }

    public function prepend(mixed $value, mixed $key = null): void
    {
        if ($key instanceof ToString) {
            $key = $key->toString();
        }

        $arr = null;
        if ($key) {
            $arr = Arr::fromArray([$key => $value]);
        } else {
            $arr = Arr::fromArray([$value]);
        }

        $this->items = $arr->merge($this->items)->asArray();
    }

    public function pull(ToString|string|int $key): mixed
    {
        if ($key instanceof ToString) {
            $key = $key->toString();
        }

        if (!isset($this->items[$key])) {
            return null;
        }

        $return = $this->items[$key];
        unset($this->items[$key]);
        return $return;
    }

    public function put(ToString|string|int $key, mixed $value): void
    {
        if($key instanceof ToString) {
            $key = $key->toString();
        }

        $this->items[$key] = $value;
    }

    public function reduceSpread(Closure $closure): static
    {
        return $this->reduce(
            function ($carry, $item) use ($closure) {
                return $closure($carry, ...$item);
            }
        );
    }

    public function reject(Closure $closure): static
    {
        return $this->filter(
            function ($item, $key) use ($closure) {
                return !$closure($item, $key);
            }
        );
    }

    public function sliding(int $windowSize, int $step = 1): static
    {
        $windows = static::create();

        for($i = 0; $i < $this->count(); $i += $step) {
            $windows[] = $this->slice($i, $windowSize);
        }

        return $windows;
    }

    public function skip(int $numberToSkip): static
    {
        return $this->slice($numberToSkip);
    }

    public function skipUntil(mixed $condition): static
    {
        if ($condition instanceof ToString) {
            $condition = $condition->toString();
        }

        for ($i = 0; $i < $this->count(); $i++) {
            $matches = false;

            if ($condition instanceof Closure) {
                $matches = $condition($this->items[$i]);
            } elseif ($condition == $this->items[$i]) {
                $matches = true;
            }

            if (!$matches) {
                continue;
            }

            return $this->slice($i);
        }

        return static::create();
    }

    public function skipWhile(mixed $condition): static
    {
        if ($condition instanceof ToString) {
            $condition = $condition->toString();
        }

        for ($i = 0; $i < $this->count(); $i++) {
            $matches = false;

            if ($condition instanceof Closure) {
                $matches = $condition($this->items[$i]);
            } elseif ($condition = $this->items[$i]) {
                $matches = true;
            }

            if ($matches) {
                continue;
            }

            return $this->slice($i);
        }

        return static::create();
    }

    public function sole(Closure|ToString|string|int $key, mixed $value = null): mixed
    {
        // @todo
    }

    public function sort(Closure $closure): bool
    {
        return usort($this->items, $closure);
    }

    public function sortByKeyAscending(ToString|string $key): bool
    {
        if ($key instanceof ToString) {
            $key = $key->toString();
        }

        return usort(
            $this->items,
            function($a, $b) use($key) {
                if ($a[$key] == $b[$key]) {
                    return 0;
                }

                return $a[$key] < $b[$key] ? -1 : 1;
            }
        );
    }

    public function sortByKeyDescending(ToString|string $key): bool
    {
        if ($key instanceof ToString) {
            $key = $key->toString();
        }

        return usort(
            $this->items,
            function($a, $b) use($key) {
                if ($a[$key] == $b[$key]) {
                    return 0;
                }

                return $a[$key] > $b[$key] ? -1 : 1;
            }
        );
    }
}
