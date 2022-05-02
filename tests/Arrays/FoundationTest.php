<?php

namespace Tests\Adapt\Foundation\Arrays;

use Adapt\Foundation\Arrays\Arr;
use PHPUnit\Framework\TestCase;
use Tests\Adapt\Foundation\Arrays\TestClasses\ToArrayAsArray;

class FoundationTest extends TestCase
{
    protected Arr $arr;

    public function __construct(?string $name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        $this->arr = new Arr([1, 2, 3, 'four', 'five', 'six']);
    }

    public function testConstructor(): void
    {
        $this->assertCount(6, $this->arr);
    }

    public function testOffsetExists(): void
    {
        $this->assertTrue($this->arr->offsetExists(5));
        $this->assertFalse($this->arr->offsetExists(6));
    }

    public function testOffsetGet(): void
    {
        $this->assertEquals('four', $this->arr->offsetGet(3));
    }

    public function testOffsetSet(): void
    {
        $this->arr->offsetSet(3, 4);
        $this->assertEquals(4, $this->arr->offsetGet(3));
    }

    public function testOffsetUnset(): void
    {
        $arr = Arr::fromArray(['one' => 1, 'two' => 2, 'three' => 3]);
        $arr->offsetUnset('three');
        $this->assertCount(2, $arr);
        $this->assertEquals(['one' => 1, 'two' => 2], $arr->toArray());
    }

    public function testCount(): void
    {
        $this->assertCount(6, $this->arr);
        $this->assertCount(0, Arr::fromArray([]));
        $this->assertCount(1, Arr::fromArray([1]));
        $this->assertCount(2, Arr::fromArray([1, 2]));
    }

    public function testSerializeUnserialise(): void
    {
        $before = $this->arr->toArray();
        $serialised = serialize($this->arr);

        $this->assertIsString($serialised);

        $arr = unserialize($serialised);

        $this->assertInstanceOf(Arr::class, $arr);
        $after = $arr->toArray();

        $this->assertEquals($before, $after);
    }

    public function testFromArray(): void
    {
        $arr = Arr::fromArray($this->arr->toArray());
        $this->assertEquals($this->arr->toArray(), $arr->toArray());
        $arr = Arr::fromArray($this->arr);
        $this->assertEquals($this->arr->toArray(), $arr->toArray());
    }

    public function testToArray(): void
    {
        $arr = Arr::fromArray([
            new ToArrayAsArray(),
            new ToArrayAsArray(),
            new ToArrayAsArray()
        ]);

        $this->assertEquals(
            [
                ['one', 'two', 'three'],
                ['one', 'two', 'three'],
                ['one', 'two', 'three'],
            ],
            $arr->toArray()
        );
    }

    public function testAsArray(): void
    {
        $arr = Arr::fromArray([
            new ToArrayAsArray(),
            new ToArrayAsArray(),
            new ToArrayAsArray()
        ]);

        $asArray = $arr->asArray();
        $this->assertCount(3, $asArray);
        $this->assertInstanceOf(ToArrayAsArray::class, $arr[0]);
    }
}
