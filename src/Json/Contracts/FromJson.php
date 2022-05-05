<?php

namespace Adapt\Foundation\Json\Contracts;

use Adapt\Foundation\Json\Json;

interface FromJson
{
    public static function fromJson(Json $json): static;
}
