<?php

namespace Tests\Adapt\Foundation\Extending;

use Adapt\Foundation\Arrays\Arr;
use PHPUnit\Framework\TestCase;

class ExtentableTraitTest extends TestCase
{
    public function testExtendingInstance(): void
    {
        Arr::extendInstance('hello', function($instance) { return 'world'; });
        $arr = Arr::create();
        $this->assertEquals('world', $arr->hello());
    }

    public function testExtendingStatic(): void
    {
        Arr::extendStatic('hello', function($instance) {return 'world';});
        $this->assertEquals('world', Arr::hello());
    }
}
