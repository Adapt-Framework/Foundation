<?php

namespace Tests\Adapt\Foundation\Json;

use Adapt\Foundation\Json\Json;
use Adapt\Foundation\Strings\Str;
use PHPUnit\Framework\TestCase;

class JsonTest extends TestCase
{
    public function testFromString(): void
    {
        $jsonString = '["hello", "world"]';
        $json = Json::fromString($jsonString);
        $this->assertCount(2, $json);
        $this->assertInstanceOf(Str::class, $json[0]);
        $this->assertInstanceOf(Str::class, $json[1]);
        $this->assertEquals('hello', $json[0]->toString());
        $this->assertEquals('world', $json[1]->toString());

        $jsonString = '{"name": {"first": "Matt", "last": "B"}, "email": "someone@example.com"}';
        $json = Json::fromString($jsonString);
        $this->assertCount(2, $json);
        $this->assertInstanceOf(Json::class, $json['name']);
        $this->assertInstanceOf(Str::class, $json['name']['first']);
        $this->assertInstanceOf(Str::class, $json['name']['last']);
    }

    public function testSet(): void
    {
        $json = Json::create();
        $json['name'] = ['first' => 'Matt', 'last' => 'B'];
        $json['email'] = 'someone@example.com';
        $this->assertCount(2, $json);
        $this->assertInstanceOf(Json::class, $json['name']);
        $this->assertInstanceOf(Str::class, $json['name']['first']);
        $this->assertInstanceOf(Str::class, $json['name']['last']);
        $this->assertEquals('Matt', $json['name']['first']->toString());
    }

    public function testToString(): void
    {
        $json = Json::create();
        $json['name'] = ['first' => 'Matt', 'last' => 'B'];
        $json['email'] = 'someone@example.com';

        $expected = '{"name":{"first":"Matt","last":"B"},"email":"someone@example.com"}';
        $this->assertEquals($expected, $json->toString());
    }
}
