<?php

namespace Tests\Adapt\Foundation\Arrays;

use Adapt\Foundation\Arrays\ArrayPath;
use Adapt\Foundation\Strings\Str;
use PHPUnit\Framework\TestCase;

class ArrayPathTest extends TestCase
{
    public function testFromString(): void
    {
        $path = ArrayPath::fromString('name.first');
        $this->assertCount(2, $path);
        $this->assertInstanceOf(Str::class, $path[0]);
        $this->assertInstanceOf(Str::class, $path[1]);
    }

    public function testToString(): void
    {
        $path = ArrayPath::create();
        $path[0] = 'name';
        $path[1] = 'first';

        $this->assertCount(2, $path);
        $this->assertInstanceOf(Str::class, $path[0]);
        $this->assertInstanceOf(Str::class, $path[1]);
        $this->assertEquals('name.first', $path->toString());
    }

    public function testExtractFromArray(): void
    {
        $array = ['name' => ['first' => 'Matt', 'last' => 'B']];
        $path = ArrayPath::fromString('name.first');
        $this->assertEquals('Matt', $path->extractFromArray($array));
        $path = ArrayPath::fromString('name.last');
        $this->assertEquals('B', $path->extractFromArray($array));
        $path = ArrayPath::fromString('name');
        $this->assertEquals(['first' => 'Matt', 'last' => 'B'], $path->extractFromArray($array));

        $array = [
            'emails' => [
                ['type' => 'Home', 'email' => 'someone@example.com']
            ]
        ];
        $path = ArrayPath::fromString('emails.0.type');
        $this->assertEquals('Home', $path->extractFromArray($array));

    }
}
