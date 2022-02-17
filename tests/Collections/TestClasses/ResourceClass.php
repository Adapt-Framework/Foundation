<?php

namespace Tests\Adapt\Foundation\Collections\TestClasses;

use Adapt\Foundation\Collections\Collection;

class ResourceClass
{
    public function __construct(public Collection $collection){}
}
