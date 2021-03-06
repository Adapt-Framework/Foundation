<?php

namespace Adapt\Foundation\FileSystem;

use Adapt\Foundation\Arrays\Contracts\AsArray;
use Adapt\Foundation\Arrays\Contracts\ToArray;
use Adapt\Foundation\Strings\Contracts\FromString;
use Adapt\Foundation\Strings\Contracts\ToString;
use Adapt\Foundation\Strings\Str;
use Adapt\Foundation\Strings\StringCollection;

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

    public function exists(): bool
    {
        return file_exists($this->toString());
    }

    public function isDirectory(): bool
    {
        return is_dir($this->toString());
    }

    public function isFile(): bool
    {
        return is_file($this->toString());
    }

    public function isLink(): bool
    {
        return is_link($this->toString());
    }

    public function isExecutable(): bool
    {
        return is_executable($this->toString());
    }

    public static function fromString(string $string): static
    {
        return new static($string);
    }

    public function toString(): string
    {
        return $this->implode(static::PATH_SEPARATOR)->toString();
    }

    public function __toString(): string
    {
        return $this->toString();
    }
}
