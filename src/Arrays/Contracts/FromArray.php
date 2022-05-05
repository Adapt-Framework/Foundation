<?php

namespace Adapt\Foundation\Arrays\Contracts;

interface FromArray
{
    /**
     * Allows the construction of the object from an array
     *
     * @param ToArray|AsArray|array $array
     * @return static
     */
    public static function fromArray(ToArray|AsArray|array $array): static;
}
