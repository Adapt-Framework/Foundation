<?php

namespace Adapt\Foundation\Extending;

use Adapt\Foundation\Singleton\SingletonTrait;
use Adapt\Foundation\Strings\ToString;
use Closure;

class ExtensionManager
{
    use SingletonTrait;

    protected array $instanceExtensions = [];
    protected array $staticExtensions = [];

    public static function extendInstance(ToString|string $class, ToString|string $method, Closure $closure): void
    {
        if ($class instanceof ToString) {
            $class = $class->toString();
        }

        if ($method instanceof ToString) {
            $method = $method->toString();
        }

        if (!isset(static::getInstance()->instanceExtensions[$class])) {
            static::getInstance()->instanceExtensions[$class] = [];
        }

        static::getInstance()->instanceExtensions[$class][$method] = $closure;
    }

    public static function extendStatic(ToString|string $class, ToString|string $method, Closure $closure): void
    {
        if ($class instanceof ToString) {
            $class = $class->toString();
        }

        if ($method instanceof ToString) {
            $method = $method->toString();
        }

        if (!isset(static::getInstance()->staticExtensions[$class])) {
            static::getInstance()->staticExtensions[$class] = [];
        }

        static::getInstance()->staticExtensions[$class][$method] = $closure;
    }

    public static function hasInstanceExtension(ToString|string $class, ToString|string $method): bool
    {
        if ($class instanceof ToString) {
            $class = $class->toString();
        }

        if ($method instanceof ToString) {
            $method = $method->toString();
        }

        return isset(self::getInstance()->instanceExtensions[$class][$method]);
    }

    public static function hasStaticExtension(ToString|string $class, ToString|string $method): bool
    {
        if ($class instanceof ToString) {
            $class = $class->toString();
        }

        if ($method instanceof ToString) {
            $method = $method->toString();
        }

        return isset(self::getInstance()->staticExtensions[$class][$method]);
    }

    public static function getInstanceExtension(ToString|string $class, ToString|string $method): Closure|null
    {
        if ($class instanceof ToString) {
            $class = $class->toString();
        }

        if ($method instanceof ToString) {
            $method = $method->toString();
        }

        if (self::hasInstanceExtension($class, $method)) {
            return self::getInstance()->instanceExtensions[$class][$method];
        }

        return null;
    }

    public static function getStaticExtension(ToString|string $class, ToString|string $method): Closure|null
    {
        if ($class instanceof ToString) {
            $class = $class->toString();
        }

        if ($method instanceof ToString) {
            $method = $method->toString();
        }

        if (self::hasStaticExtension($class, $method)) {
            return self::getInstance()->staticExtensions[$class][$method];
        }

        return null;
    }
}
