<?php

namespace Adapt\Foundation\FileSystem;


use Adapt\Foundation\Arrays\AsArray;
use Adapt\Foundation\Arrays\ToArray;
use Adapt\Foundation\Collections\Collection;
use Adapt\Foundation\Strings\FromString;
use Adapt\Foundation\Strings\ToString;

class Directory extends Collection implements FromString
{
    public Path $path;

    public function __construct(Path|ToArray|AsArray|array|ToString|string $path)
    {
        if ($path instanceof ToArray || $path instanceof  AsArray || is_array($path)) {
            $path = Path::fromArray($path);
        }

        if ($path instanceof ToString) {
            $path = $path->toString();
        }

        if (is_string($path)) {
            $path = Path::fromString($path);
        }

        $this->path = $path;
        parent::__construct();
        $this->readDir();
    }

    public function current(): static|File
    {
        $current = parent::current();
        $path = $this->path->collect();
        $path[] = $current;
        if (is_dir($path->toString())) {
            return new static($path);
        }

        return File::openForReading($path);
    }

    public function offsetGet(mixed $offset): static|File
    {
        $filename = parent::offsetGet($offset);
        $path = $this->path->collect();
        $path[] = $filename;

        if (is_dir($path->toString())) {
            return new static($path);
        }

        return File::openForReading($path);
    }

    public function makeDir(): bool
    {
        if ($this->exists()) {
            return false;
        }

        $basePath = '';

        foreach($this->path as $part) {
            $basePath .= '/' . $part->toString();
            $dir = Path::fromString($basePath);
            if ($dir->exists()) {
                continue;
            }

            mkdir($dir->toString());
        }

        return true;
    }

    public function parent(): static|null
    {
        if ($this->path->isEmpty()) {
            return null;
        }

        $path = $this->path->collect();
        $path->pop();
        return new static($path);
    }

    public function exists(): bool
    {
        return $this->path->exists();
    }

    public static function fromString(ToString|string $string): static
    {
        if ($string instanceof ToString) {
            $string = $string->toString();
        }

        return new static(Path::fromString($string));
    }

    protected function readDir(): void
    {
        if (!$this->exists() && !is_dir($this->path->toString())) {
            return;
        }

        $this->items = array_values(array_filter(
            scandir($this->path->toString()),
            function($item) {
                return !in_array($item, ['.', '..']);
            }
        ));
    }
}
