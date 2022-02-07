<?php

namespace Adapt\Foundation\Collections;

use Adapt\Foundation\Arrays\Arr;
use Adapt\Foundation\Strings\ToString;

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
        return $this->column($key)->filter()->sum()  / $this->count();
    }
}
