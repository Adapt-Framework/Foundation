<?php

namespace Adapt\Foundation\FileSystem;

use Adapt\Foundation\Arrays\AsArray;
use Adapt\Foundation\Arrays\ToArray;
use Adapt\Foundation\Collections\Collection;
use Adapt\Foundation\Streams\FileStream;
use Adapt\Foundation\Strings\Str;
use Adapt\Foundation\Strings\StringCollection;
use Adapt\Foundation\Strings\ToString;

class File implements FileStream
{
    public Path $path;
    public string $mode;
    protected mixed $handle = null;

    public function __construct(Path|ToString|string $path, ToString|string $mode = FileMode::READ_ONLY)
    {
        if ($path instanceof Path) {
            $this->path = $path;
        }elseif ($path instanceof ToString) {
            $this->path = Path::fromString($path->toString());
        }elseif (is_string($path)) {
            $this->path = Path::fromString($path);
        }

        if ($mode instanceof ToString) {
            $this->mode = $mode->toString();
        } else {
            $this->mode = $mode;
        }
    }

    public function __destruct()
    {
        if ($this->handle) {
            @fclose($this->handle);
        }
    }

    protected function getHandle(): mixed
    {
        if (!is_null($this->handle)) {
            return $this->handle;
        }

        $this->open();
        return $this->handle;
    }

    public function isReadable(): bool
    {
        if (is_null($this->handle) && $this->path->exists()) {
            return is_readable($this->path->toString());
        }

        return match ($this->mode) {
            FileMode::READ_ONLY, FileMode::READ_AND_WRITE, FileMode::WRITE_AND_READ, FileMode::APPEND_AND_READ,
            FileMode::CREATE_AND_WRITE_AND_READ, FileMode::CREATE_AND_READ_WRITE_FROM_START => true,
            default => false
        };
    }

    public function isWritable(): bool
    {
        if (is_null($this->handle) && $this->path->exists()) {
            return is_writable($this->path->toString());
        }

        return match ($this->mode) {
            FileMode::READ_AND_WRITE, FileMode::WRITE_ONLY, FileMode::WRITE_AND_READ, FileMode::APPEND_ONLY,
            FileMode::APPEND_AND_READ, FileMode::CREATE_AND_WRITE, FileMode::CREATE_AND_WRITE_AND_READ,
            FileMode::CREATE_AND_WRITE_FROM_START, FileMode::CREATE_AND_READ_WRITE_FROM_START => true,
            default => false
        };
    }

    public function isOpen(): bool
    {
        return (bool)$this->handle;
    }


    public function all(): Str|false
    {
        return static::readAll($this->path);
    }

    public function open(): bool
    {
        if ($this->handle) {
            return true;
        }

        $this->handle = fopen($this->path->toString(), $this->mode);
        return (bool)$this->handle;
    }

    public function lock(int $operation, int &$wouldBlock): bool
    {
        $handle = $this->getHandle();
        if (!$handle) {
            return false;
        }

        return flock($handle, $operation, $wouldBlock);
    }

    public function seek(int $offset, int $whence = SEEK_SET): int|false
    {
        $handle = $this->getHandle();
        if (!$handle) {
            return false;
        }

        return fseek($handle, $offset, $whence);
    }

    public function stat(): Collection|false
    {
        $handle = $this->getHandle();
        if (!$handle) {
            return false;
        }

        $return = fstat($handle);
        if (!$return) {
            return false;
        }

        return Collection::fromArray($return);
    }

    public function tell(): int|false
    {
        $handle = $this->getHandle();
        if (!$handle) {
            return false;
        }

        return ftell($handle);
    }

    public function truncate(int $size): bool
    {
        $handle = $this->getHandle();
        if (!$handle) {
            return false;
        }

        if (!$this->isWritable()) {
            return false;
        }

        return ftruncate($handle, $size);
    }

    public function close(): bool
    {
        if ($this->handle) {
            $return = fclose($this->handle);
            $this->handle = null;
            return $return;
        }

        return true;
    }

    public function eof(): bool
    {
        $handle = $this->getHandle();

        if ($handle) {
            return feof($handle);
        }

        return true;
    }

    public function flush(): bool
    {
        $handle = $this->getHandle();

        if ($handle) {
            return fflush($handle);
        }

        return false;
    }

    public function read(int $length): Str|false
    {
        $handle = $this->getHandle();

        if (!$handle) {
            return false;
        }

        if (!$this->isReadable()) {
            return false;
        }

        $value = fread($handle, $length);
        if (!$value) {
            return false;
        }

        return Str::fromString($value);
    }

    public function readCharacter(): string|false
    {
        $handle = $this->getHandle();

        if (!$handle) {
            return false;
        }

        if (!$this->isReadable()) {
            return false;
        }

        return fgetc($handle);
    }

    public function readCsvRecord(
        int|null $length = null,
        ToString|string $separator = ",",
        ToString|string $enclosure = "\"",
        ToString|string $escape = '\\'
    ): StringCollection|false
    {
        $handle = $this->getHandle();

        if (!$handle) {
            return false;
        }

        if (!$this->isReadable()) {
            return false;
        }

        $return = fgetcsv(
            $handle,
            $length,
            is_string($separator) ? $separator : $separator->toString(),
            is_string($enclosure) ? $enclosure : $enclosure->toString(),
            is_string($escape) ? $escape : $escape->toString()
        );

        if (!$return) {
            return false;
        }

        return StringCollection::fromArray($return);
    }

    public function readFormat(ToString|string $format, &...$vars): StringCollection|int|false|null
    {
        $handle = $this->getHandle();

        if (!$handle) {
            return false;
        }

        if (!$this->isReadable()) {
            return false;
        }

        $return = fscanf(
            $handle,
            is_string($format) ? $format : $format->toString(),
            ...$vars
        );

        if (is_array($return)) {
            return StringCollection::fromArray($return);
        }

        return $return;
    }

    public function readLine(int|null $length = null): Str|false
    {
        $handle = $this->getHandle();
        if (!$handle) {
            return false;
        }

        if (!$this->isReadable()) {
            return false;
        }

        $return = fgets($handle, $length);
        if (!$return) {
            return false;
        }

        return Str::fromString($return);
    }

    public function passThrough(): int|false
    {
        $handle = $this->getHandle();
        if (!$handle) {
            return false;
        }

        return fpassthru($handle);
    }

    public function writeCsvRecord(
        ToArray|array $fields,
        ToString|string $separator = ",",
        ToString|string $enclosure = "\"",
        ToString|string $escape = '\\'
    ): int|false
    {
        $handle = $this->getHandle();
        if (!$handle) {
            return false;
        }

        if (!$this->isWritable()) {
            return false;
        }

        return fputcsv(
            $handle,
            is_array($fields) ? $fields : $fields->toArray(),
            is_string($separator) ? $separator : $separator->toString(),
            is_string($enclosure) ? $enclosure : $enclosure->toString(),
            is_string($escape) ? $escape : $escape->toString()
        );
    }

    public function write(ToString|string $data, ?int $length = null): int|false
    {
        $handle = $this->getHandle();
        if (!$handle) {
            return false;
        }

        if (!$this->isWritable()) {
            return false;
        }

        return fwrite(
            $handle,
            is_string($data) ? $data : $data->toString(),
            $length
        );
    }

    public function rewind(): bool
    {
        $handle = $this->getHandle();
        if (!$handle) {
            return false;
        }

        return rewind($handle);
    }

    public static function openForReading(Path|ToString|string $file): static
    {
        return new static($file, FileMode::READ_ONLY);
    }

    public static function openForWriting(Path|ToString|string $file): static
    {
        return new static($file, FileMode::WRITE_ONLY);
    }

    public static function openForReadingAndWriting(Path|ToString|string $file): static
    {
        return new static($file, FileMode::READ_AND_WRITE);
    }

    public static function openForAppending(Path|ToString|string $file): static
    {
        return new static($file, FileMode::APPEND_ONLY);
    }

    public static function openForAppendingAndReading(Path|ToString|string $file): static
    {
        return new static($file, FileMode::APPEND_AND_READ);
    }

    public static function createForWriting(Path|ToString|string $file): static
    {
        return new static($file, FileMode::CREATE_AND_WRITE);
    }

    public static function createForWritingAndReading(Path|ToString|string $file): static
    {
        return new static($file, FileMode::CREATE_AND_WRITE_AND_READ);
    }

    public static function readAll(Path|ToString|string $file): Str|false
    {
        if ($file instanceof ToString && !$file instanceof Path) {
            $file = Path::fromString($file->toString());
        } elseif (is_string($file)) {
            $file = Path::fromString($file);
        }

        if (!$file->exists()) {
            return false;
        }

        return Str::fromString(file_get_contents($file->toString()));
    }

    public static function writeWithContent(Path|ToString|string $file, ToString|string $content): static|false
    {
        if ($file instanceof ToString && !$file instanceof Path) {
            $file = Path::fromString($file->toString());
        } elseif (is_string($file)) {
            $file = Path::fromString($file);
        }

        if ($content instanceof ToString) {
            $content = $content->toString();
        }

        if (file_put_contents($file->toString(), $content) === false) {
            return false;
        }

        return static::openForAppending($file);
    }

    public function prependReadFilter(ToString|string $name, AsArray|array $options = []): mixed
    {
        $handle = $this->getHandle();
        if (!$handle) {
            return false;
        }

        return stream_filter_prepend($handle, $name, STREAM_FILTER_READ, $options);
    }

    public function prependWriteFilter(ToString|string $name, AsArray|array $options = []): mixed
    {
        $handle = $this->getHandle();
        if (!$handle) {
            return false;
        }

        return stream_filter_prepend($handle, $name, STREAM_FILTER_WRITE, $options);
    }

    public function appendReadFilter(ToString|string $name, AsArray|array $options = []): mixed
    {
        $handle = $this->getHandle();
        if (!$handle) {
            return false;
        }

        return stream_filter_append($handle, $name, STREAM_FILTER_READ, $options);
    }

    public function appendWriteFilter(ToString|string $name, AsArray|array $options = []): mixed
    {
        $handle = $this->getHandle();
        if (!$handle) {
            return false;
        }

        return stream_filter_append($handle, $name, STREAM_FILTER_WRITE, $options);
    }

    public function removeFilter(mixed $filter): bool
    {
        return stream_filter_remove($filter);
    }
}
