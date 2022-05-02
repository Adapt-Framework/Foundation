<?php

namespace Adapt\Foundation\Container;

use Adapt\Foundation\Arrays\AsArray;
use Adapt\Foundation\Strings\ToString;
use Closure;

class ContextualBinding
{
    public ToString|string|null $tag = null;
    public Closure|AsArray|ToString|array|string|null $give = null;
    public ToString|string|null $class = null;


    public static function create(): static
    {
        return new static();
    }

    public function needs(ToString|string $class): static
    {
        $this->class = $class;
    }

    public function give(Closure|AsArray|ToString|array|string $give): static
    {
        $this->give = $give;
    }

    public function giveTagged(ToString|string $tag): static
    {
        $this->tag = $tag;
    }
}
