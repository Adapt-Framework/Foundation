<?php

namespace Adapt\Foundation\Arrays;

interface FromArray
{
    public static function fromArray(ToArray|AsArray|array $array): static;
}
