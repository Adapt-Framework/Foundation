<?php

namespace Adapt\Foundation\Arrays\Contracts;

interface FromArray
{
    public static function fromArray(ToArray|AsArray|array $array): static;
}
