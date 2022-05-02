<?php

namespace Adapt\Foundation\Attributes;

trait GuardableTrait
{
    protected bool $isGuarded = true;

    public function guard(): void
    {
        $this->isGuarded = true;
    }

    public function unguard(): void
    {
        $this->isGuarded = false;
    }
}
