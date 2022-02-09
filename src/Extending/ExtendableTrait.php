<?php

namespace Adapt\Foundation\Extending;

use Adapt\Foundation\Strings\ToString;
use Closure;

trait ExtendableTrait
{
    public static function extendInstance(ToString|string $method, Closure $closure): void
    {
        ExtensionManager::extendInstance(static::class, $method, $closure);
    }

    public static function extendStatic(ToString|string $method, Closure $closure): void
    {
        ExtensionManager::extendStatic(static::class, $method, $closure);
    }

    public function __call($name, $args)
    {
        if (!ExtensionManager::hasInstanceExtension(static::class, $name)) {
            return null;
        }

        $closure = ExtensionManager::getInstanceExtension(static::class, $name);
        $params = array_merge([$this], $args);
        return $closure(...$params);
    }

    public static function __callStatic($name, $args)
    {
        if (!ExtensionManager::hasInstanceExtension(static::class, $name)) {
            return null;
        }

        $closure = ExtensionManager::getStaticExtension(static::class, $name);
        $params = array_merge([static::class], $args);
        return $closure(...$params);
    }
}
