<?php

namespace Adapt\Foundation\Collections;

use Adapt\Foundation\Arrays\Arr;
use Adapt\Foundation\Arrays\Contracts\AsArray;
use Adapt\Foundation\Arrays\Contracts\ToArray;
use Adapt\Foundation\Arrays\Foundation;
use Adapt\Foundation\Comparison\Compare;
use Adapt\Foundation\Strings\Contracts\ToString;
use Adapt\Foundation\Strings\Str;
use Closure;

class Collection extends Arr
{
    public function all(): array
    {
        return $this->asArray();
    }

    public function average(ToString|string|null $key = null): float|int
    {
        if (!$this->count()) {
            return 0;
        }

        if (is_null($key)) {
            return $this->filter()->sum() / $this->count();
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
                    $output[] = static::fromArray(array_merge($item, [$a]));
                }
            }
        }
        return $output;
    }

    public function doesntContain(mixed $item): bool
    {
        return !$this->contains($item);
    }

    public function duplicates(ToString|string|null $key = null): static
    {
        $duplicates = static::create();
        $uniqueItems = static::create();

        $values = $this->collect();
        if ($key) {
            if ($key instanceof ToString) {
                $key = $key->toString();
            }

            $values = $values->column($key);
        }

        $values->each(function ($value, $key) use (&$duplicates, &$uniqueItems) {
            if ($uniqueItems->values()->in($value)) {
                $duplicates[$key] = $value;
            } else {
                $uniqueItems[$key] = $value;
            }
        });

        return $duplicates;
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
                if (Compare::test($item[$key], Compare::EQUALS, $valueOrOperator)) {
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
        return $this->map($closure)->collapse();
    }

    public function flatten(int|null $depth = null): static
    {
        if (is_null($depth)) {
            $depth = 1;
        }

        $output = static::create();

        foreach($this->items as $item) {
            if (is_array($item)) {
                if ($depth === 1) {
                    $output = $output->merge(array_values($item));
                } else {
                    $output = $output->merge(static::fromArray($item)->flatten(--$depth));
                }

            } else {
                $output[] = $item;
            }
        }

        return $output;
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
                $groupKey = $item[$groupValue];
                if (!isset($output[$groupKey])) {
                    $output[$groupKey] = static::create();
                }

                if (!$output[$groupKey]->in($item)) {
                    $output[$groupKey][] = $item;
                }
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

        return Str::fromString(implode($keyOrGlue, $this->items));
    }

    public function isEmpty(): bool
    {
        return !(bool)$this->count();
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

            $keyName = is_string($key) ? $item[$key] : $key($item);
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
            if (is_array($item) || $item instanceof AsArray) {
                return $closure(...$item);
            }

            return $closure($item);
        });
    }

    public function mapToGroup(Closure $closure): static
    {
        $output = static::create();
        foreach ($this->items as $key => $value) {
            $processed = $closure($value, $key);
            $processedKey = array_keys($processed)[0];
            $processed[$processedKey] = [$processed[$processedKey]];
            if (isset($output[$processedKey])) {
                $output[$processedKey] = array_merge($output[$processedKey], $processed[$processedKey]);
            } else {
                $output = $output->merge($processed);
            }
        }

        return $output;
    }

    public function mapWithKeys(Closure $closure): static
    {
        $output = static::create();
        $this->each(function ($item, $key) use (&$output, $closure) {
            $output = $output->merge($closure($item, $key));
        });
        return $output;
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

    public function min(ToString|string|null $key = null): mixed
    {
        if (!$key) {
            return min($this->items);
        }

        return min($this->column($key)->toArray());
    }

    public function mode(ToString|string|null $key = null): mixed
    {
        $collection = $this->collect();
        if ($key) {
            $collection = $collection->column($key);
        }
        $counts = $collection->countBy();
        $counts->sortDescending(true);

        if ($counts->isEmpty()) {
            return null;
        }

        $highestCount = $counts[$counts->keys()[0]];
        $returnValues = [$counts->keys()[0]];

        for($i = 1; $i < $counts->count(); $i++) {
            $index = $counts->keys()[$i];
            if ($counts[$index] !== $highestCount) {
                continue;
            }

            $returnValues[] = $counts->keys()[$i];
        }

        if (count($returnValues) === 1) {
            return $returnValues[0];
        }

        return $returnValues;
    }

    public function nth(int $every, int|null $offset = null): static
    {
        $output = static::create();
        for($i = $offset ?? 0; $i < $this->count(); $i += $every) {
            $index = $this->keys()[$i];
            $output[] = $this[$index];
        }

        return $output;
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

    public function prepend(mixed $value, mixed $key = null): static
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

        return static::fromArray($arr->merge($this->items)->asArray());
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

    public function put(ToString|string|int $key, mixed $value): static
    {
        if($key instanceof ToString) {
            $key = $key->toString();
        }

        $this->items[$key] = $value;
        return $this;
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
            if ($i + $windowSize > $this->count()) {
                break;
            }
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
        $filtered = null;
        if ($key instanceof Closure) {
            $filtered = $this->filter($key)->values();
        } elseif ($key && $value) {
            $filtered = $this->filter(function ($item, $index) use ($key, $value) {
                if (!is_array($item) && !$item instanceof ToArray) {
                    return false;
                }

                if (!isset($item[$key])) {
                    return false;
                }

                return $item[$key] === $value;
            })->values();
        }

        if (!$filtered) {
            return false;
        }

        if ($filtered->count() === 1) {
            return $filtered[0];
        }

        return false;
    }

    public function sort(Closure $closure = null): static
    {
        if (!$closure) {
            return $this->sortAscending();
        }
        $items = $this->items;
        usort($items, $closure);
        return static::fromArray($items);
    }

    public function sortByKeyAscending(ToString|string $key): static
    {
        $items = $this->items;

        if ($key instanceof ToString) {
            $key = $key->toString();
        }

        usort(
            $items,
            function($a, $b) use($key) {
                if ($a[$key] == $b[$key]) {
                    return 0;
                }

                return $a[$key] < $b[$key] ? -1 : 1;
            }
        );

        return static::fromArray($items);
    }

    public function sortByKeyDescending(ToString|string $key): static
    {
        $items = $this->items;

        if ($key instanceof ToString) {
            $key = $key->toString();
        }

        usort(
            $items,
            function($a, $b) use($key) {
                if ($a[$key] == $b[$key]) {
                    return 0;
                }

                return $a[$key] > $b[$key] ? -1 : 1;
            }
        );

        return static::fromArray($items);
    }

    public function split(int $chunks, bool $preserveKeys = false): static
    {
        if ($this->count() === 0) {
            return static::create();
        }

        $chunkLength = ceil($this->count() / $chunks);
        return $this->chunk($chunkLength, $preserveKeys);
    }

    public function take(int $number): static
    {
        return $this->slice(0, $number);
    }

    public function takeUntil(mixed $condition): static
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

            return $this->slice(0, $i);
        }

        return $this->collect();
    }

    public function takeWhile(mixed $condition): static
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

            return $this->slice(0, $i);
        }

        return $this->collect();
    }

    public function tap(Closure $closure): static
    {
        $closure($this);
        return $this;
    }

    public static function times(int $number, Closure $closure): static
    {
        return static::fromArray(
            array_map($closure, static::range(1, $number)->toArray())
        );
    }

    public function transform(Closure $closure): static
    {
        $this->items = array_map($closure, $this->items);
        return $this;
    }

    public function union(AsArray|array $array): static
    {
        if ($this->isAssoc()) {
            return $this->merge($this->diffAssoc($array));
        }

        return $this->merge($this->diff($array));
    }

    public function when(bool $condition, Closure $then, Closure|null $else = null): static
    {
        if ($condition) {
            return $then($this, $condition);
        } elseif ($else) {
            return $else($this, $condition);
        }

        return $this;
    }

    public function whenEmpty(Closure $then, Closure|null $else = null): static
    {
        if ($this->isEmpty()) {
            return $then($this);
        } elseif ($else) {
            return $else($this);
        }

        return $this;
    }

    public function whenNotEmpty(Closure $then, Closure|null $else = null): static
    {
        if (!$this->isEmpty()) {
            return $then($this);
        } elseif ($else) {
            return $else($this);
        }

        return $this;
    }

    public function where(ToString|string $key, mixed $valueOrOperator = null, mixed $value = null): static
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

        return $this->filter(function($item) use ($key, $valueOrOperator, $value) {
            if (!$item instanceof Foundation && !is_array($item)) {
                return false;
            }

            if (!isset($item[$key])) {
                return false;
            }

            if ($valueOrOperator && $value) {
                return Compare::test($item[$key], $valueOrOperator, $value);
            }

            if ($valueOrOperator) {
                return Compare::test($item[$key], Compare::EQUALS, $valueOrOperator);
            }

            return true;
        })->values();
    }

    public function whereBetween(ToString|string $key, mixed $min, mixed $max): static
    {
        if ($key instanceof ToString) {
            $key = $key->toString();
        }

        if ($min instanceof ToString) {
            $min = $min->toString();
        }

        if ($max instanceof ToString) {
            $max = $max->toString();
        }

        return $this->filter(function($item) use ($key, $min, $max) {
            if (!$item instanceof Foundation && !is_array($item)) {
                return false;
            }

            if (!isset($item[$key])) {
                return false;
            }

            if ($item[$key] < $min) {
                return false;
            }

            if ($item[$key] > $max) {
                return false;
            }

            return true;
        })->values();
    }

    public function whereIn(ToString|string $key, AsArray|array $array): static
    {
        if ($key instanceof ToString) {
            $key = $key->toString();
        }

        if ($array instanceof AsArray) {
            $array = $array->asArray();
        }

        return $this->filter(function($item) use ($key, $array) {
            if (!$item instanceof Foundation && !is_array($item)) {
                return false;
            }

            if (!isset($item[$key])) {
                return false;
            }

            return in_array($item[$key], $array);
        })->values();
    }

    public function whereInstanceOf(ToString|string $class): static
    {
        if ($class instanceof ToString) {
            $class = $class->toString();
        }

        return $this->filter(function($item) use ($class) {
            return $item instanceof $class;
        });
    }

    public function whereNotBetween(ToString|string $key, mixed $min, mixed $max): static
    {
        if ($key instanceof ToString) {
            $key = $key->toString();
        }

        if ($min instanceof ToString) {
            $min = $min->toString();
        }

        if ($max instanceof ToString) {
            $max = $max->toString();
        }

        return $this->filter(function($item) use ($key, $min, $max) {
            if (!$item instanceof Foundation && !is_array($item)) {
                return false;
            }

            if (!isset($item[$key])) {
                return true;
            }

            if ($item[$key] < $min) {
                return true;
            }

            if ($item[$key] > $max) {
                return true;
            }

            return false;
        })->values();
    }

    public function whereNotIn(ToString|string $key, AsArray|array $array): static
    {
        if ($key instanceof ToString) {
            $key = $key->toString();
        }

        if ($array instanceof AsArray) {
            $array = $array->asArray();
        }

        return $this->filter(function($item) use ($key, $array) {
            if (!$item instanceof Foundation && !is_array($item)) {
                return false;
            }

            if (!isset($item[$key])) {
                return true;
            }

            return !in_array($item[$key], $array);
        })->values();
    }

    public function whereNotNull(ToString|string $key): static
    {
        if ($key instanceof ToString) {
            $key = $key->toString();
        }

        return $this->filter(function($item) use ($key) {
            if (!$item instanceof Foundation && !is_array($item)) {
                return false;
            }

            if (!isset($item[$key])) {
                return false;
            }

            return !is_null($item[$key]);
        })->values();
    }

    public function whereNull(ToString|string $key): static
    {
        if ($key instanceof ToString) {
            $key = $key->toString();
        }

        return $this->filter(function($item) use ($key) {
            if (!$item instanceof Foundation && !is_array($item)) {
                return false;
            }

            if (!isset($item[$key])) {
                return true;
            }

            return is_null($item[$key]);
        })->values();
    }
}
