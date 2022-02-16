<?php

namespace Adapt\Foundation\Streams;

use Adapt\Foundation\Arrays\ToArray;
use Adapt\Foundation\Strings\Str;
use Adapt\Foundation\Strings\StringCollection;
use Adapt\Foundation\Strings\ToString;

interface Stream
{
    public function isReadable(): bool;
    public function isWritable(): bool;
    public function isOpen(): bool;
    public function close(): bool;
    public function eof(): bool;
    public function flush(): bool;
    public function read(int $length): Str|false;
    public function readCharacter(): string|false;
    public function readCsvRecord(
        int|null $length = null,
        ToString|string $separator = ",",
        ToString|string $enclosure = "\"",
        ToString|string $escape = '\\'
    ): StringCollection|false;
    public function readFormat(ToString|string $format, mixed &...$vars): StringCollection|int|false|null; // fscanf
    public function readLine(int|null $length = null): Str|false;
    public function passThrough(): int|false;
    public function writeCsvRecord(
        ToArray|array $fields,
        ToString|string $separator = ",",
        ToString|string $enclosure = "\"",
        ToString|string $escape = '\\'
    ): int|false;
    public function write(ToString|string $data, int|null $length = null): int|false;


}
