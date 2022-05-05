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

        $this->assertTrue($dt->isWeekend);
        $this->assertFalse($dt->isWeekday);
        $this->assertTrue($dt->isSunday);
        $this->assertFalse($dt->isMonday);
        $this->assertFalse($dt->isTuesday);
        $this->assertFalse($dt->isWednesday);
        $this->assertFalse($dt->isThursday);
        $this->assertFalse($dt->isFriday);
        $this->assertFalse($dt->isSaturday);

        $dt->goToTomorrow();
        $this->assertFalse($dt->isWeekend);
        $this->assertTrue($dt->isWeekday);
        $this->assertFalse($dt->isSunday);
        $this->assertTrue($dt->isMonday);
        $this->assertFalse($dt->isTuesday);
        $this->assertFalse($dt->isWednesday);
        $this->assertFalse($dt->isThursday);
        $this->assertFalse($dt->isFriday);
        $this->assertFalse($dt->isSaturday);

        $dt->goToTomorrow();
        $this->assertFalse($dt->isWeekend);
        $this->assertTrue($dt->isWeekday);
        $this->assertFalse($dt->isSunday);
        $this->assertFalse($dt->isMonday);
        $this->assertTrue($dt->isTuesday);
        $this->assertFalse($dt->isWednesday);
        $this->assertFalse($dt->isThursday);
        $this->assertFalse($dt->isFriday);
        $this->assertFalse($dt->isSaturday);

        $dt->goToTomorrow();
        $this->assertFalse($dt->isWeekend);
        $this->assertTrue($dt->isWeekday);
        $this->assertFalse($dt->isSunday);
        $this->assertFalse($dt->isMonday);
        $this->assertFalse($dt->isTuesday);
        $this->assertTrue($dt->isWednesday);
        $this->assertFalse($dt->isThursday);
        $this->assertFalse($dt->isFriday);
        $this->assertFalse($dt->isSaturday);

        $dt->goToTomorrow();
        $this->assertFalse($dt->isWeekend);
        $this->assertTrue($dt->isWeekday);
        $this->assertFalse($dt->isSunday);
        $this->assertFalse($dt->isMonday);
        $this->assertFalse($dt->isTuesday);
        $this->assertFalse($dt->isWednesday);
        $this->assertTrue($dt->isThursday);
        $this->assertFalse($dt->isFriday);
        $this->assertFalse($dt->isSaturday);

        $dt->goToTomorrow();
        $this->assertFalse($dt->isWeekend);
        $this->assertTrue($dt->isWeekday);
        $this->assertFalse($dt->isSunday);
        $this->assertFalse($dt->isMonday);
        $this->assertFalse($dt->isTuesday);
        $this->assertFalse($dt->isWednesday);
        $this->assertFalse($dt->isThursday);
        $this->assertTrue($dt->isFriday);
        $this->assertFalse($dt->isSaturday);

        $dt->goToTomorrow();
        $this->assertTrue($dt->isWeekend);
        $this->assertFalse($dt->isWeekday);
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
        $dt = DateTime::fromString('00:00:00');
        $this->assertTrue($dt->isMorning);
        $this->assertFalse($dt->isAfternoon);

        $dt->hour = 12;
        $this->assertFalse($dt->isMorning);
        $this->assertTrue($dt->isAfternoon);
    }

    public function testDaysInMonth(): void
    {
        $dt = DateTime::fromString('2022-01-01');
        for($month = DateTime::JANUARY; $month <= DateTime::DECEMBER; $month++) {
            $dt->month = $month;
            $this->assertEquals(
                match ($month) {
                    DateTime::JANUARY, DateTime::MARCH, DateTime::MAY, DateTime::JULY, DateTime::AUGUST, DateTime::OCTOBER, DateTime::DECEMBER => 31,
                    DateTime::FEBRUARY => 28,
                    DateTime::APRIL, DateTime::JUNE, DateTime::SEPTEMBER, DateTime::NOVEMBER => 30
                },
                $dt->daysInMonth
            );
        }
    }

    public function testLeapYears(): void
    {
        $dt = DateTime::fromString('2023-02-01');
        $this->assertFalse($dt->isLeapYear);
        $this->assertEquals(28, $dt->daysInMonth);

        $dt->year = 2024;
        $this->assertTrue($dt->isLeapYear);
        $this->assertEquals(29, $dt->daysInMonth);
    }

    public function testAdding(): void
    {
        $dt = DateTime::fromString('2000-01-01 00:00:00');

        $dt->addYears(1);
        $this->assertEquals(2001, $dt->year);

        $dt->addMonths(1);
        $this->assertEquals(2, $dt->month);

        $dt->addDays(1);
        $this->assertEquals(2, $dt->day);

        $dt->addHours(1);
        $this->assertEquals(1, $dt->hour);

        $dt->addMinutes(1);
        $this->assertEquals(1, $dt->minute);

        $dt->addSeconds(1);
        $this->assertEquals(1, $dt->second);

        $dt->addMicroseconds(100);
        $this->assertEquals(100, $dt->microsecond);

        $dt = DateTime::fromString('1999-12-31 23:59:59');
        $dt->addSeconds(1);
        $this->assertEquals('2000-01-01 00:00:00', $dt->toString());
    }

    public function testSubtracting(): void
    {
        $dt = DateTime::fromString('2000-01-01 00:00:00');

        $dt->subYears(1);
        $this->assertEquals(1999, $dt->year);

        $dt->subMonths(1);
        $this->assertEquals(12, $dt->month);

        $dt->subDays(1);
        $this->assertEquals(30, $dt->day);

        $dt->subHours(1);
        $this->assertEquals(23, $dt->hour);

        $dt->subMinutes(1);
        $this->assertEquals(59, $dt->minute);

        $dt->subSeconds(1);
        $this->assertEquals(59, $dt->second);

        $dt->subMicroseconds(1);
        $this->assertEquals(999999, $dt->microsecond);
    }

    public function testTense(): void
    {
        $dt = DateTime::now();

        $this->assertTrue($dt->isToday);
        $this->assertFalse($dt->isYesterday);
        $this->assertFalse($dt->isTomorrow);

        $dt->subDays(1);
        $this->assertFalse($dt->isToday);
        $this->assertTrue($dt->isYesterday);
        $this->assertFalse($dt->isTomorrow);

        $dt->addDays(2);
        $this->assertFalse($dt->isToday);
        $this->assertFalse($dt->isYesterday);
        $this->assertTrue($dt->isTomorrow);
    }

    public function testDayMovement(): void
    {
        $dt = DateTime::fromString('2022-05-05 21:49:58');

        $dt->goBackDays(4);
        $this->assertEquals('2022-05-01', $dt->format(DateTime::FORMAT_DATE));

        $dt->goForwardDays(9);
        $this->assertEquals('2022-05-10', $dt->format(DateTime::FORMAT_DATE));

        $dt->goToTomorrow();
        $this->assertEquals('2022-05-11', $dt->format(DateTime::FORMAT_DATE));

        $dt->goToYesterday();
        $this->assertEquals('2022-05-10', $dt->format(DateTime::FORMAT_DATE));
    }

    public function testWorkingDayMovement(): void
    {
        $dt = DateTime::fromString('2022-05-05 21:49:58');

        $dt->goBackWorkingDays(4);
        $this->assertEquals('2022-04-29', $dt->format(DateTime::FORMAT_DATE));

        $dt->goForwardWorkingDays(9);
        $this->assertEquals('2022-05-12', $dt->format(DateTime::FORMAT_DATE));
    }

    public function testMovingToDayOfWeek(): void
    {
        $dt = DateTime::fromString('2022-05-05 21:49:58');

        $dt->goBackToDayDayOfWeek(DateTime::SUNDAY);
        $this->assertEquals('2022-05-01', $dt->format(DateTime::FORMAT_DATE));

        $dt->goForwardToDayOfWeek(DateTime::FRIDAY);
        $this->assertEquals('2022-05-06', $dt->format(DateTime::FORMAT_DATE));
    }

    public function testMoveToDayInMonth(): void
    {
        $dt = DateTime::fromString('2022-05-05 21:49:58');

        $dt->goToFirstDayInMonth();
        $this->assertEquals(1, $dt->day);

        $dt->goToFirstDayInMonth(DateTime::FRIDAY);
        $this->assertEquals(6, $dt->day);
        $this->assertTrue($dt->isFriday);

        $dt->goToSecondDayInMonth(DateTime::WEDNESDAY);
        $this->assertEquals(11, $dt->day);
        $this->assertTrue($dt->isWednesday);

        $dt->goToThirdDayInMonth(DateTime::SUNDAY);
        $this->assertEquals(15, $dt->day);
        $this->assertTrue($dt->isSunday);

        $dt->goToFourthDayInMonth(DateTime::MONDAY);
        $this->assertEquals(23, $dt->day);
        $this->assertTrue($dt->isMonday);

        $dt->goToLastDayInMonth();
        $this->assertEquals(31, $dt->day);

        $dt->goToLastDayInMonth(DateTime::TUESDAY);
        $this->assertEquals(31, $dt->day);
        $this->assertTrue($dt->isTuesday);

        $dt->goToLastDayInMonth(DateTime::FRIDAY);
        $this->assertEquals(27, $dt->day);
        $this->assertTrue($dt->isFriday);
    }

    public function testFirstLastWorkingDayInMonth(): void
    {
        $dt = DateTime::fromString('2022-05-05');

        $dt->goToFirstWorkingDayInMonth();
        $this->assertEquals(2, $dt->day);

        $dt->goToLastWorkingDayInMonth();
        $this->assertEquals(31, $dt->day);

        $dt->month = DateTime::JULY;
        $dt->goToFirstWorkingDayInMonth();
        $this->assertEquals(1, $dt->day);

        $dt->goToLastWorkingDayInMonth();
        $this->assertEquals(29, $dt->day);
    }
}
