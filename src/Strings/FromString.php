<?php

namespace Adapt\Foundation\Strings;

interface FromString
{
    public static function fromString(string $string): static;
}
