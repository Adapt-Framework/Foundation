<?php

namespace Adapt\Foundation\Json;

interface FromJson
{
    public static function fromJson(Json $json): static;
}
