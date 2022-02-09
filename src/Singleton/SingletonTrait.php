<?php

namespace Adapt\Foundation\Singleton;

trait SingletonTrait
{
    protected static self|null $instance = null;

    public static function getInstance(): static
    {
        if (is_null(self::$instance)) {
            self::$instance = new static();
        }

        return self::$instance;
    }
}
