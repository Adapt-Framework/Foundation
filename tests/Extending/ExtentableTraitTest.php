<?php

namespace Tests\Adapt\Foundation\Extending;

use Adapt\Foundation\Arrays\Arr;
use PHPUnit\Framework\TestCase;

class ExtentableTraitTest extends TestCase
{
    public function testExtending(): void
    {
        Arr::extendInstance('hello', function($instance) { return 'world'; });
        $arr = Arr::create();
        $this->assertEquals('world', $arr->hello());
    }
}
