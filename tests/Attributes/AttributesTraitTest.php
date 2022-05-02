<?php

namespace Tests\Adapt\Foundation\Attributes;

use Adapt\Foundation\Dates\Date;
use Adapt\Foundation\Dates\DateTime;
use Adapt\Foundation\Dates\Time;
use Adapt\Foundation\Strings\Str;
use PHPUnit\Framework\TestCase;
use Tests\Adapt\Foundation\Attributes\TestClasses\AttributeTestClass;

class AttributesTraitTest extends TestCase
{
    public function testGet(): void
    {
        $testClass = new AttributeTestClass(
            ['id' => 1, 'username' => 'matt', 'date_created' => '2012-12-13', 'updated_at' => '2022-05-02 18:49:00', 'time' => '12:11:10'],
            null,
            null,
            ['date_created'],
            ['updated_at'],
            ['time']
        );

        $this->assertIsInt($testClass->id);
        $this->assertEquals(1, $testClass->id);
        $this->assertInstanceOf(Str::class, $testClass->username);
        $this->assertEquals('matt', $testClass->username->toString());
        $this->assertInstanceOf(Date::class, $testClass->date_created);
        $this->assertEquals('2012-12-13', $testClass->date_created->toString());
        $this->assertInstanceOf(DateTime::class, $testClass->updated_at);
        $this->assertEquals('2022-05-02 18:49:00', $testClass->updated_at->toString());
        $this->assertInstanceOf(Time::class, $testClass->time);
        $this->assertEquals('12:11:10', $testClass->time->toString());
    }

    public function testSet(): void
    {
        $testClass = new AttributeTestClass(
            ['id' => 1, 'username' => 'matt', 'date_created' => '2012-12-13', 'updated_at' => '2022-05-02 18:49:00', 'time' => '12:11:10'],
            null,
            null,
            ['date_created'],
            ['updated_at'],
            ['time']
        );

        $testClass->id = 2;
        $this->assertEquals(2, $testClass->id);

        $testClass->username = 'mattb';
        $this->assertInstanceOf(Str::class, $testClass->username);
        $this->assertEquals('mattb', $testClass->username->toString());
    }
}
