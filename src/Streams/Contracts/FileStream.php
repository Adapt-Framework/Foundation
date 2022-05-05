<?php

namespace Adapt\Foundation\Streams\Contracts;

use Adapt\Foundation\Collections\Collection;
use Adapt\Foundation\FileSystem\FileMode;
use Adapt\Foundation\FileSystem\Path;
use Adapt\Foundation\Strings\Contracts\ToString;

interface FileStream extends Stream
{
    public function __construct(Path|ToString|string $path, ToString|string $mode = FileMode::READ_ONLY);
    public function open(): bool;
    public function lock(int $operation, int &$wouldBlock): bool;
    public function seek(int $offset, int $whence = SEEK_SET): int|false;
    public function stat(): Collection|false;
    public function tell(): int|false;
    public function truncate(int $size): bool;
    public function rewind(): bool;
}
