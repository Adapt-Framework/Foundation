<?php

namespace Adapt\Foundation\Arrays\Contracts;

interface ToArray
{
    /**
     * Returns the elements as an array, if the element supports `toArray()` then it is also called on the child
     * in a recursive manner.
     *
     * @return array
     */
    public function toArray(): array;
}
