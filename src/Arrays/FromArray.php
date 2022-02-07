<?php

namespace Adapt\Foundation\Arrays;

interface FromArray
{
    public static function fromArray(array|ToArray $array): static;
}
