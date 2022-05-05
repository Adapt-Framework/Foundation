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

        $testClass->date_created = '2021-10-12';
        $this->assertInstanceOf(Date::class, $testClass->date_created);
        $this->assertEquals(2021, $testClass->date_created->year);
        $this->assertEquals(10, $testClass->date_created->month);
        $this->assertEquals(12, $testClass->date_created->day);

        $testClass->updated_at = DateTime::fromString('2022-05-03 08:00:00');
        $this->assertInstanceOf(DateTime::class, $testClass->updated_at);
        $this->assertEquals('2022-05-03 08:00:00', $testClass->updated_at->toString());

        $testClass->time = '08:02:17';
        $this->assertInstanceOf(Time::class, $testClass->time);
        $this->assertEquals('08:02:17', $testClass->time->toString());

        $testClass->newField = "Hello World";
        $this->assertInstanceOf(Str::class, $testClass->newField);
        $this->assertEquals('Hello World', $testClass->newField->toString());

        $testClass->newInt = 1234;
        $this->assertEquals(1234, $testClass->newInt);

        $testClass->newFloat = 123.4;
        $this->assertEquals(123.4, $testClass->newFloat);
    }

    public function testFill(): void
    {
        $testClass = new AttributeTestClass(
            ['id' => 1, 'username' => 'matt', 'date_created' => '2012-12-13', 'updated_at' => '2022-05-02 18:49:00', 'time' => '12:11:10'],
            ['username', 'date_created', 'updated_at', 'time', 'newField'],
            null,
            ['date_created'],
            ['updated_at'],
            ['time']
        );

        $newData = [
            'id' => 2,
            'username' => 'mattb',
            'updated_at' => '2022-05-03 08:10:11',
            'date_created' => '2012-01-02',
            'time' => '10:11:12',
            'newField' => 'Hello world',
            'newField2' => 'Foobar'
        ];

        $testClass->fill($newData);

        $this->assertEquals(1, $testClass->id);
        $this->assertInstanceOf(Str::class, $testClass->username);
        $this->assertEquals('mattb', $testClass->username->toString());
        $this->assertInstanceOf(DateTime::class, $testClass->updated_at);
        $this->assertEquals('2022-05-03 08:10:11', $testClass->updated_at->toString());
        $this->assertInstanceOf(Date::class, $testClass->date_created);
        $this->assertEquals('2012-01-02', $testClass->date_created->toString());
        $this->assertInstanceOf(Time::class, $testClass->time);
        $this->assertEquals('10:11:12', $testClass->time->toString());
        $this->assertInstanceOf(Str::class, $testClass->newField);
        $this->assertEquals('Hello world', $testClass->newField->toString());
        $this->assertNull($testClass->newField2);

        $testClass = new AttributeTestClass([], null, null, ['date_created'], ['updated_at'], ['time']);
        $testClass->fill($newData);

        $this->assertEquals(2, $testClass->id);
        $this->assertInstanceOf(Str::class, $testClass->username);
        $this->assertEquals('mattb', $testClass->username->toString());
        $this->assertInstanceOf(DateTime::class, $testClass->updated_at);
        $this->assertEquals('2022-05-03 08:10:11', $testClass->updated_at->toString());
        $this->assertInstanceOf(Date::class, $testClass->date_created);
        $this->assertEquals('2012-01-02', $testClass->date_created->toString());
        $this->assertInstanceOf(Time::class, $testClass->time);
        $this->assertEquals('10:11:12', $testClass->time->toString());
        $this->assertInstanceOf(Str::class, $testClass->newField);
        $this->assertEquals('Hello world', $testClass->newField->toString());
        $this->assertInstanceOf(Str::class, $testClass->newField2);
        $this->assertEquals('Foobar', $testClass->newField2->toString());
    }

    public function testRecordedUnrecordedFill(): void
    {
        $testClass = new AttributeTestClass(
            ['id' => 1, 'username' => 'matt', 'date_created' => '2012-12-13', 'updated_at' => '2022-05-02 18:49:00', 'time' => '12:11:10'],
            null,
            null,
            ['date_created'],
            ['updated_at'],
            ['time']
        );

        $newData = [
            'id' => 2,
            'username' => 'mattb',
            'updated_at' => '2022-05-03 08:10:11',
            'date_created' => '2012-01-02',
            'time' => '10:11:12',
            'newField' => 'Hello world',
            'newField2' => 'Foobar'
        ];

        $testClass->performResetChangeLog();
        $testClass->fill($newData);
        $this->assertCount(7, $testClass->getChanges());

        $testClass = new AttributeTestClass(
            ['id' => 1, 'username' => 'matt', 'date_created' => '2012-12-13', 'updated_at' => '2022-05-02 18:49:00', 'time' => '12:11:10'],
            null,
            null,
            ['date_created'],
            ['updated_at'],
            ['time']
        );

        $newData = [
            'id' => 2,
            'username' => 'mattb',
            'updated_at' => '2022-05-03 08:10:11',
            'date_created' => '2012-01-02',
            'time' => '10:11:12',
            'newField' => 'Hello world',
            'newField2' => 'Foobar'
        ];
        $testClass->performResetChangeLog();
        $testClass->fill($newData, false);
        $this->assertCount(0, $testClass->getChanges());
    }

    public function testGuardedFill(): void
    {
        $testClass = new AttributeTestClass(
            ['id' => 1, 'username' => 'matt', 'date_created' => '2012-12-13', 'updated_at' => '2022-05-02 18:49:00', 'time' => '12:11:10'],
            null,
            ['id'],
            ['date_created'],
            ['updated_at'],
            ['time']
        );

        $newData = [
            'id' => 2,
            'username' => 'mattb',
            'updated_at' => '2022-05-03 08:10:11',
            'date_created' => '2012-01-02',
            'time' => '10:11:12',
        ];

        $testClass->fill($newData);

        $this->assertEquals(1, $testClass->id);
        $this->assertInstanceOf(Str::class, $testClass->username);
        $this->assertEquals('mattb', $testClass->username->toString());
        $this->assertInstanceOf(DateTime::class, $testClass->updated_at);
        $this->assertEquals('2022-05-03 08:10:11', $testClass->updated_at->toString());
        $this->assertInstanceOf(Date::class, $testClass->date_created);
        $this->assertEquals('2012-01-02', $testClass->date_created->toString());
        $this->assertInstanceOf(Time::class, $testClass->time);
        $this->assertEquals('10:11:12', $testClass->time->toString());
    }

    public function testHasChanged(): void
    {
        $testClass = new AttributeTestClass(
            ['id' => 1, 'username' => 'matt', 'date_created' => '2012-12-13', 'updated_at' => '2022-05-02 18:49:00', 'time' => '12:11:10'],
            null,
            ['id'],
            ['date_created'],
            ['updated_at'],
            ['time']
        );

        $testClass->performResetChangeLog();

        $this->assertFalse($testClass->hasChanged());

        $newData = [
            'id' => 2
        ];

        $testClass->fill($newData);
        $this->assertFalse($testClass->hasChanged());

        $newData = [
            'username' => 'mattb',
            'updated_at' => '2022-05-03 08:10:11',
        ];

        $testClass->fill($newData);
        $this->assertTrue($testClass->hasChanged());
    }

    public function testChangeLog(): void
    {
        $testClass = new AttributeTestClass(
            ['id' => 1, 'username' => 'matt', 'date_created' => '2012-12-13', 'updated_at' => '2022-05-02 18:49:00', 'time' => '12:11:10'],
            null,
            null,
            ['date_created'],
            ['updated_at'],
            ['time']
        );

        $testClass->performResetChangeLog();

        $newData = [
            'username' => 'mattb',
            'updated_at' => '2022-05-03 08:10:11',
            'newField' => 'Hello'
        ];

        $testClass->fill($newData);
        $changes = $testClass->getChanges();

        $this->assertCount(3, $changes);
        $this->assertArrayHasKey('username', $changes);
        $this->assertArrayHasKey('updated_at', $changes);
        $this->assertArrayHasKey('newField', $changes);

        $this->assertEquals('matt', $changes['username']['from']->toString());
        $this->assertEquals('mattb', $changes['username']['to']->toString());

        $this->assertEquals('2022-05-02 18:49:00', $changes['updated_at']['from']->toString());
        $this->assertEquals('2022-05-03 08:10:11', $changes['updated_at']['to']->toString());
        $this->assertNull($changes['newField']['from']);
        $this->assertInstanceOf(Str::class, $changes['newField']['to']);
        $this->assertEquals('Hello', $changes['newField']['to']->toString());
    }
}
