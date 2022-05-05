<?php

namespace Adapt\Foundation\Attributes;

use Adapt\Foundation\Arrays\Contracts\ToArray;
use Adapt\Foundation\Dates\Date;
use Adapt\Foundation\Dates\DateTime;
use Adapt\Foundation\Dates\Time;
use Adapt\Foundation\Strings\Contracts\ToString;
use Adapt\Foundation\Strings\Str;

trait AttributesTrait
{
    use GuardableTrait;

    protected array $attributes = [];
    protected array|null $fillable = null;
    protected array|null $guardedAttributes = null;
    protected array|null $dateAttributes = [];
    protected array|null $dateTimeAttributes = [];
    protected array|null $timeAttributes = [];
    protected bool $hasChanged = false;
    protected array $changes = [];

    public function fill(ToArray|array $attributes, bool $recordChanges = true): void
    {
        $hasChanged = $this->hasChanged;
        $changes = $this->changes;

        $fillable = $this->fillable;
        $guarded = $this->isGuarded ? $this->guardedAttributes : [];

        foreach($attributes as $key => $value) {
            if ($fillable) {
                if (!in_array($key, $fillable)) {
                    continue;
                }
            }

            if ($guarded && count($guarded)) {
                if (in_array($key, $guarded)) {
                    continue;
                }
            }

            $this->$key = $value;
        }

        if (!$recordChanges) {
            $this->hasChanged = $hasChanged;
            $this->changes = $changes;
        }
    }

    public function hasChanged(): bool
    {
        return $this->hasChanged;
    }

    protected function resetChangeLog(): void
    {
        $this->hasChanged = false;
        $this->changes = [];
    }

    public function __get(string $key): mixed
    {
        $overrideMethods = [
            sprintf('get%sAttribute', $key),
            sprintf('get_%s_attribute', $key),
        ];

        foreach($overrideMethods as $method) {
            if (method_exists($this, $method)) {
                return $this->$method();
            }
        }

        if (isset($this->attributes[$key])) {
            return $this->attributes[$key];
        }

        return null;
    }

    public function __set(string $key, mixed $value): void
    {
        $overrideMethods = [
            sprintf('get%sAttribute', $key),
            sprintf('get_%s_attribute', $key),
        ];

        foreach($overrideMethods as $method) {
            if (method_exists($this, $method)) {
                $this->$method($value);
                return;
            }
        }

        if (is_null($value)) {
            if (is_null($this->$key) && isset($this->attributes[$key])) {
                return;
            }

            $this->changes[$key] = ['from' => $this->$key, 'to' => $value];
            $this->attributes[$key] = $value;
            $this->hasChanged = true;
            return;
        }

        if (is_numeric($value)) {
            if (!is_float($value) && !is_int($value)) {
                if (strval((float)$value) == $value) {
                    $value = (float)$value;
                } else {
                    $value = (int)$value;
                }
            }

            if ($this->$key !== $value) {
                $this->changes[$key] = ['from' => $this->$key, 'to' => $value];
                $this->attributes[$key] = $value;
                $this->hasChanged = true;
                return;
            }
        }

        if (in_array($key, $this->dateAttributes)) {
            if (!$value instanceof Date) {
                $value = Date::fromString($value);
            }

            if ($this->$key && $this->$key->toString() === $value->toString()) {
                return;
            }

            $this->changes[$key] = ['from' => $this->$key, 'to' => $value];
            $this->attributes[$key] = $value;
            $this->hasChanged = true;
            return;
        }

        if (in_array($key, $this->dateTimeAttributes)) {


            if (!$value instanceof DateTime) {
                $value = DateTime::fromString($value);
            }

            if ($this->$key && $this->$key->toString() === $value->toString()) {
                return;
            }

            $this->changes[$key] = ['from' => $this->$key, 'to' => $value];
            $this->attributes[$key] = $value;
            $this->hasChanged = true;
            return;
        }

        if (in_array($key, $this->timeAttributes)) {
            if (!$value instanceof Time) {
                $value = Time::fromString($value);
            }

            if ($this->$key && $this->$key->toString() === $value->toString()) {
                return;
            }

            $this->changes[$key] = ['from' => $this->$key, 'to' => $value];
            $this->attributes[$key] = $value;
            $this->hasChanged = true;
            return;
        }

        if ($value instanceof ToString && !$value instanceof Str) {
            $value = $value->toString();
        }

        if (is_string($value)) {
            $value = Str::fromString($value);
        }

        if ($value instanceof Str) {
            if ($this->$key && $this->$key->toString() === $value->toString()) {
                return;
            }

            $this->changes[$key] = ['from' => $this->$key, 'to' => $value];
            $this->attributes[$key] = $value;
            $this->hasChanged = true;
            return;
        }

        //  Array?
        return;
    }
}
