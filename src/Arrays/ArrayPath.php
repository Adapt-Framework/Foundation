<?php

namespace Adapt\Foundation\Arrays;

use Adapt\Foundation\FileSystem\Path;
use Adapt\Foundation\Strings\FromString;
use Adapt\Foundation\Strings\Str;
use Adapt\Foundation\Strings\StringCollection;
use Adapt\Foundation\Strings\ToString;

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
