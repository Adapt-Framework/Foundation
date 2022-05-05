<?php

namespace Adapt\Foundation\Arrays;

use Adapt\Foundation\Arrays\Contracts\AsArray;
use Adapt\Foundation\Arrays\Contracts\ToArray;
use Adapt\Foundation\FileSystem\Path;

class ArrayPath extends Path
{
    protected const PATH_SEPARATOR = '.';

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
}
