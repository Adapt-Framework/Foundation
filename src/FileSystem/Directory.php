<?php

namespace Adapt\Foundation\FileSystem;

use Adapt\Foundation\Strings\FromString;
use Adapt\Foundation\Strings\StringCollection;
use Adapt\Foundation\Strings\ToString;

class Directory extends StringCollection implements ToString, FromString
{
    public Path $path;


}
