<?php

namespace Tests\Adapt\Foundation\Strings;

use Adapt\Foundation\Strings\Str;
use Adapt\Foundation\Strings\StringCollection;
use PHPUnit\Framework\TestCase;

class StringCollectionTest extends TestCase
{
    public function testFromArray(): void
    {
        $collection = StringCollection::fromArray(['Hello', 'World']);
        $this->assertCount(2, $collection);
        $this->assertInstanceOf(Str::class, $collection[0]);
        $this->assertInstanceOf(Str::class, $collection[1]);
        $this->assertEquals('Hello', $collection[0]->toString());
        $this->assertEquals('World', $collection[1]->toString());

    }

    public function testSetString(): void
    {
        $collection = StringCollection::create();
        $collection[0] = 'Hello world';
        $this->assertInstanceOf(Str::class, $collection[0]);
    }
}
