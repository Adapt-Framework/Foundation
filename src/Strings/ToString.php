<?php

namespace Adapt\Foundation\Strings;

use Stringable;

interface ToString extends Stringable
{
    public function toString(): string;
}
