<?php

namespace Adapt\Foundation\Strings\Contracts;

interface FromString
{
    public static function fromString(string $string): static;
}
