<?php

namespace Tests\Adapt\Foundation\Dates;

use Adapt\Foundation\Dates\DateTime;
use PHPUnit\Framework\TestCase;

class DateTimeTest extends TestCase
{
    public function testFromString(): void
    {
        $dt = DateTime::fromString('2022-04-30');
        $this->assertEquals('2022-04-30', $dt->format('Y-m-d'));

        $dt = DateTime::fromString('30th April 2022');
        $this->assertEquals('2022-04-30', $dt->format('Y-m-d'));
    }

    public function testGetters(): void
    {
        $dt = DateTime::fromString('2022-04-30 20:17:18');
        $this->assertEquals('2022-04-30 20:17:18', $dt->format('Y-m-d H:i:s'));

        $this->assertEquals(2022, $dt->year);
        $this->assertEquals(4, $dt->month);
        $this->assertEquals(30, $dt->day);
        $this->assertEquals(20, $dt->hour);
        $this->assertEquals(17, $dt->minute);
        $this->assertEquals(18, $dt->second);
        $this->assertEquals(0, $dt->microsecond);
        $this->assertTrue($dt->isWeekend);
        $this->assertFalse($dt->isWeekday);
        $this->assertEquals(DateTime::SATURDAY, $dt->dayOfWeek);
    }
}
