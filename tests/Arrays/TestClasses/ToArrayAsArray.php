<?php

namespace Tests\Adapt\Foundation\Arrays\TestClasses;

use Adapt\Foundation\Arrays\AsArray;
use Adapt\Foundation\Arrays\ToArray;

class ToArrayAsArray implements ToArray, AsArray
{
    public function asArray(): array
    {
        return ['one', 'two', 'three'];
    }

    public function toArray(): array
    {
        return ['one', 'two', 'three'];
    }

}
