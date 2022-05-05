<?php

namespace Adapt\Foundation\Json\Contracts;

use Adapt\Foundation\Json\Json;

interface ToJson
{
    public function toJson(): Json;
}
