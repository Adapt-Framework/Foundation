<?php

namespace Adapt\Foundation\Container;

use Adapt\Foundation\Arrays\Contracts\AsArray;
use Adapt\Foundation\Collections\Collection;
use ArrayAccess;
use Closure;

class Container implements ArrayAccess
{
    protected Collection $singletons;
    protected Collection $bindings;
    protected Collection $instances;
    protected Collection $tags;

    public function __construct()
    {
        $this->singletons = Collection::create();
        $this->bindings = Collection::create();
        $this->instances = Collection::create();
        $this->tags = Collection::create();
    }

    public function bind(string $class, Closure|string $closureOrClass): void
    {
        if ($this->instances->has($class)) {
            unset($this->instances[$class]);
        }

        $this->bindings[$class] = $closureOrClass;
    }

    public function bindSingleton(string $class, Closure $closure): void
    {
        if (!$this->singletons->in($class)) {
            $this->singletons[] = $class;
        }

        $this->bind($class, $closure);
    }

    public function bindInstance(string $class, mixed $instance): void
    {
        if (!$this->singletons->in($class)) {
            $this->singletons[] = $class;
        }

        $this->instances[$class] = $instance;
    }

    public function bindWhen(AsArray|array|string $classes): ContextualBinding
    {
        $context = ContextualBinding::create();

        if (is_string($classes)) {
            $classes = [$classes];
        }

        foreach($classes as $class) {
            $this->bind(
                $class,
                function (Container $container, array $params) use (&$context) {
                    // ...
                }
            );
        }

        return $context;
    }

    public function tag(AsArray|array|string $classes, string $tagName): void
    {
        $this->tags[$tagName] = Collection::fromArray(is_string($classes) ? [$classes] : $classes);
    }

    public function tagged(string $tagName): array
    {
        return $this->tags[$tagName] ? $this->tags[$tagName]->asArray() : [];
    }

    public function make(string $class): mixed
    {
        return $this->resolve($class);
    }

    public function makeWith(string $class, AsArray|array $params): mixed
    {
        return $this->resolve($class, $params);
    }

    public function factory(string $class): Closure
    {
        $container = $this;
        return function (AsArray|array $params = []) use ($container, $class) {
            return $container->makeWith($class, $params);
        };
    }

    protected function resolve(string $class, AsArray|array $params = []): mixed
    {
        $singleton = $this->singletons->in($class);
        if ($singleton && $this->instances->has($class)) {
            return $this->instances[$class];
        }

        if (!$this->bindings->has($class)) {
            return null;
        }

        $resolved = $this->bindings[$class]($params instanceof AsArray ? $params->asArray() : $params);
        if ($singleton) {
            $this->instances[$class] = $resolved;
        }

        return $resolved;
    }

    public function offsetExists(mixed $offset)
    {
        return $this->bindings->has($offset);
    }

    public function offsetGet(mixed $offset)
    {
        return $this->make($offset);
    }

    public function offsetSet(mixed $offset, mixed $value)
    {
        $this->bind(
            $offset,
            $value instanceof Closure ? $value : function() use ($value) {
                return $value;
            }
        );
    }

    public function offsetUnset(mixed $offset)
    {
        unset($this->bindings, $this->instances, $this->singletons);
    }


}
