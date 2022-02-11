<?php

namespace Adapt\Foundation\FileSystem;

use Adapt\Foundation\Arrays\AsArray;
use Adapt\Foundation\Arrays\ToArray;
use Adapt\Foundation\Strings\FromString;
use Adapt\Foundation\Strings\Str;
use Adapt\Foundation\Strings\StringCollection;
use Adapt\Foundation\Strings\ToString;

class Path extends StringCollection implements ToString, FromString
{
    protected const PATH_SEPARATOR = DIRECTORY_SEPARATOR;

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

        parent::__construct($path->explode(static::PATH_SEPARATOR)->toArray());
    }

    public static function fromString(string $string): static
    {
        return new static($string);
    }

    public function toString(): string
    {
        return $this->implode(static::PATH_SEPARATOR)->toString();
    }
}
