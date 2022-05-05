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
        $this->assertFalse($dt->isInTheFuture);
        $this->assertTrue($dt->isInThePast);
        $this->assertFalse($dt->isToday);
        $this->assertFalse($dt->isTomorrow);
        $this->assertFalse($dt->isYesterday);
        $this->assertFalse($dt->isMorning);
        $this->assertTrue($dt->isAfternoon);
        $this->assertFalse($dt->isLeapYear);
    }

    public function testSetters(): void
    {
        $dt = DateTime::fromString('2022-04-30 20:17:18');
        $dt->year = 1980;
        $dt->month = DateTime::JUNE;
        $dt->day = 14;
        $dt->hour = 21;
        $dt->minute = 6;
        $dt->second = 0;
        $dt->microsecond = 123456;

        $this->assertEquals('1980-06-14 21:06:00.123456', $dt->format('Y-m-d H:i:s.u'));
    }

    public function testDaysOfWeek(): void
    {
        $dt = DateTime::fromString('2022-05-01');

        $this->assertTrue($dt->isSunday);
        $this->assertFalse($dt->isMonday);
        $this->assertFalse($dt->isTuesday);
        $this->assertFalse($dt->isWednesday);
        $this->assertFalse($dt->isThursday);
        $this->assertFalse($dt->isFriday);
        $this->assertFalse($dt->isSaturday);

        $dt->goToTomorrow();
        $this->assertFalse($dt->isSunday);
        $this->assertTrue($dt->isMonday);
        $this->assertFalse($dt->isTuesday);
        $this->assertFalse($dt->isWednesday);
        $this->assertFalse($dt->isThursday);
        $this->assertFalse($dt->isFriday);
        $this->assertFalse($dt->isSaturday);

        $dt->goToTomorrow();
        $this->assertFalse($dt->isSunday);
        $this->assertFalse($dt->isMonday);
        $this->assertTrue($dt->isTuesday);
        $this->assertFalse($dt->isWednesday);
        $this->assertFalse($dt->isThursday);
        $this->assertFalse($dt->isFriday);
        $this->assertFalse($dt->isSaturday);

        $dt->goToTomorrow();
        $this->assertFalse($dt->isSunday);
        $this->assertFalse($dt->isMonday);
        $this->assertFalse($dt->isTuesday);
        $this->assertTrue($dt->isWednesday);
        $this->assertFalse($dt->isThursday);
        $this->assertFalse($dt->isFriday);
        $this->assertFalse($dt->isSaturday);

        $dt->goToTomorrow();
        $this->assertFalse($dt->isSunday);
        $this->assertFalse($dt->isMonday);
        $this->assertFalse($dt->isTuesday);
        $this->assertFalse($dt->isWednesday);
        $this->assertTrue($dt->isThursday);
        $this->assertFalse($dt->isFriday);
        $this->assertFalse($dt->isSaturday);

        $dt->goToTomorrow();
        $this->assertFalse($dt->isSunday);
        $this->assertFalse($dt->isMonday);
        $this->assertFalse($dt->isTuesday);
        $this->assertFalse($dt->isWednesday);
        $this->assertFalse($dt->isThursday);
        $this->assertTrue($dt->isFriday);
        $this->assertFalse($dt->isSaturday);

        $dt->goToTomorrow();
        $this->assertFalse($dt->isSunday);
        $this->assertFalse($dt->isMonday);
        $this->assertFalse($dt->isTuesday);
        $this->assertFalse($dt->isWednesday);
        $this->assertFalse($dt->isThursday);
        $this->assertFalse($dt->isFriday);
        $this->assertTrue($dt->isSaturday);
    }

    public function testTimeOfDay(): void
    {

    }

    public function testDaysInMonth(): void
    {

    }

    public function testLeapYears(): void
    {

    }


}
