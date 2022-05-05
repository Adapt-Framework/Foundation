<?php

namespace Adapt\Foundation\Strings\Contracts;

use Stringable;

interface ToString extends Stringable
{
    public function toString(): string;
}
