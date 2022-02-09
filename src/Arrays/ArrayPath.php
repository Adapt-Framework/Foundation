<?php

namespace Adapt\Foundation\Arrays;

use Adapt\Foundation\Strings\FromString;
use Adapt\Foundation\Strings\Str;
use Adapt\Foundation\Strings\StringCollection;
use Adapt\Foundation\Strings\ToString;

class ArrayPath extends StringCollection implements ToString, FromString
{
    public function __construct(ToArray|AsArray|array|Str|string|null $path = null)
    {
        if (!$path) {
            parent::__construct([]);
            return;
        }

        if ($path instanceof AsArray) {
            parent::__construct($path);
            return;
        }

        if ($path instanceof ToArray) {
            parent::__construct($path->toArray());
            return;
        }

        if (is_array($path)) {
            parent::__construct($path);
            return;
        }

        if (is_string($path)) {
            $path = Str::fromString($path);
        }

        parent::__construct($path->explode('.')->toArray());
    }

    public function extractFromArray(AsArray|ToArray|array $array): mixed
    {
        for($i = 0; $i < $this->count(); $i++) {
            $key = $this[$i]->toString();

            if (!isset($array[$key])) {
                return null;
            }

            $array = $array[$key];
        }

        return $array;
    }

    public static function fromString(string $string): static
    {
        return new static($string);
    }

    public function toString(): string
    {
        return $this->implode('.')->toString();
    }


}
