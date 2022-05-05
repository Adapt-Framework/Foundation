<?php

namespace Adapt\Foundation\Dates;

use DateTime as NativeDateTime;
use Adapt\Foundation\Strings\Contracts\FromString;
use Adapt\Foundation\Strings\Contracts\ToString;
use Adapt\Foundation\Strings\Str;

/**
 * @property int $year
 * @property int $month
 * @property int $day
 * @property int $hour
 * @property int $minute
 * @property int $second
 * @property int $microsecond
 * @property-read int $timestamp
 * @property-read int $dayOfWeek
 * @property-read bool $isWeekend
 * @property-read bool $isWeekday
 * @property-read bool $isInTheFuture
 * @property-read bool $isInThePast
 * @property-read bool $isToday
 * @property-read bool $isTomorrow
 * @property-read bool $isYesterday
 * @property-read bool $isMorning
 * @property-read bool $isAfternoon
 * @property-read bool $isLeapYear
 * @property-read int $daysInMonth
 * @property-read bool $isMonday
 * @property-read bool $isTuesday
 * @property-read bool $isWednesday
 * @property-read bool $isThursday
 * @property-read bool $isFriday
 * @property-read bool $isSaturday
 * @property-read bool $isSunday
 */
class DateTime extends NativeDateTime implements ToString, FromString
{
    public const FORMAT_DATE = 'Y-m-d';
    public const FORMAT_TIME = 'H:i:s';
    public const FORMAT_DATETIME = 'Y-m-d H:i:s';

    public const FORMAT_ATOM = self::ATOM;
    public const FORMAT_ISO_8601_DATE = self::FORMAT_DATE;
    public const FORMAT_ISO_8601_DATETIME = self::ATOM;
    public const FORMAT_RFC_822_DATETIME = self::RFC822;
    public const FORMAT_RFC_850_DATETIME = self::RFC850;
    public const FORMAT_RFC_1036_DATETIME = self::RFC1036;
    public const FORMAT_RFC_1123_DATETIME = self::RFC1123;
    public const FORMAT_RFC_2822_DATETIME = self::RFC2822;
    public const FORMAT_RFC_3339_DATETIME = self::RFC3339;
    public const FORMAT_RFC_7231_DATETIME = self::RFC7231;
    public const FORMAT_RSS_DATETIME = self::RSS;
    public const FORMAT_W3C_DATETIME = self::W3C;



    public const SUNDAY = 0;
    public const MONDAY = 1;
    public const TUESDAY = 2;
    public const WEDNESDAY = 3;
    public const THURSDAY = 4;
    public const FRIDAY = 5;
    public const SATURDAY = 6;

    public const JANUARY = 1;
    public const FEBRUARY = 2;
    public const MARCH = 3;
    public const APRIL = 4;
    public const MAY = 5;
    public const JUNE = 6;
    public const JULY = 7;
    public const AUGUST = 8;
    public const SEPTEMBER = 9;
    public const OCTOBER = 10;
    public const NOVEMBER = 11;
    public const DECEMBER = 12;

    public string $defaultFormat = self::FORMAT_DATETIME;

    public function addYears(int $years): self
    {
        return $this->modify(sprintf('+%s years', $years));
    }

    public function subYears(int $years): self
    {
        return $this->modify(sprintf('-%s years', $years));
    }

    public function setYear(int $year): self
    {
        return $this->setDate($year, $this->month, $this->day);
    }

    public function getYear(): int
    {
        return (int)$this->format('Y');
    }

    public function addMonths(int $months): self
    {
        return $this->modify(sprintf('+%s months', $months));
    }

    public function subMonths(int $months): self
    {
        return $this->modify(sprintf('-%s months', $months));
    }

    public function setMonth(int $month): self
    {
        return $this->setDate($this->year, $month, $this->day);
    }

    public function getMonth(): int
    {
        return (int)$this->format('m');
    }

    public function addDays(int $days): self
    {
        return $this->modify(sprintf('+%s days', $days));
    }

    public function subDays(int $days): self
    {
        return $this->modify(sprintf('-%s days', $days));
    }

    public function setDay(int $day): self
    {
        return $this->setDate($this->year, $this->month, $day);
    }

    public function getDay(): int
    {
        return (int)$this->format('d');
    }

    public function addHours(int $hours): self
    {
        return $this->modify(sprintf('+%s hours', $hours));
    }

    public function subHours(int $hours): self
    {
        return $this->modify(sprintf('-%s hours', $hours));
    }

    public function setHour(int $hour): self
    {
        return $this->setTime($hour, $this->minute, $this->second, $this->microsecond);
    }

    public function getHour(): int
    {
        return (int)$this->format('H');
    }

    public function addMinutes(int $minutes): self
    {
        return $this->modify(sprintf('+%s minutes', $minutes));
    }

    public function subMinutes(int $minutes): self
    {
        return $this->modify(sprintf('-%s minutes', $minutes));
    }

    public function setMinute(int $minute): self
    {
        return $this->setTime($this->hour, $minute, $this->second, $this->microsecond);
    }

    public function getMinute(): int
    {
        return (int)$this->format('i');
    }

    public function addSeconds(int $seconds): self
    {
        return $this->modify(sprintf('+%s seconds', $seconds));
    }

    public function subSeconds(int $seconds): self
    {
        return $this->modify(sprintf('-%s seconds', $seconds));
    }

    public function setSecond(int $second): self
    {
        return $this->setTime($this->hour, $this->minute, $second, $this->microsecond);
    }

    public function getSecond(): int
    {
        return (int)$this->format('s');
    }

    public function addMicroseconds(int $microseconds): self
    {
        return $this->modify(sprintf('+%s microseconds', $microseconds));
    }

    public function subMicroseconds(int $microseconds): self
    {
        return $this->modify(sprintf('-%s microseconds', $microseconds));
    }

    public function setMicrosecond(int $microsecond): self
    {
        return $this->setTime($this->hour, $this->minute, $this->second, $microsecond);
    }

    public function getMicrosecond(): int
    {
        return (int)$this->format('u');
    }

    public function getDayOfWeek(): int
    {
        return (int)$this->format('w');
    }

    public function isWeekend(): bool
    {
        return in_array($this->dayOfWeek, [self::SUNDAY, self::SATURDAY]);
    }

    public function isWeekday(): bool
    {
        return !$this->isWeekend();
    }

    public function isInTheFuture(): bool
    {
        return $this->timestamp > time();
    }

    public function isInThePast(): bool
    {
        return $this->timestamp < time();
    }

    public function isToday(): bool
    {
        return $this->format(self::FORMAT_DATE) === static::now()->format(self::FORMAT_DATE);
    }

    public function isTomorrow(): bool
    {
        if (!$this->isInTheFuture) {
            return false;
        }

        return $this->format(self::FORMAT_DATE) === static::now()->addDays(1)->format(self::FORMAT_DATE);
    }

    public function isYesterday(): bool
    {
        if (!$this->isInThePast) {
            return false;
        }

        return $this->format(self::FORMAT_DATE) === static::now()->subDays(1)->format(self::FORMAT_DATE);
    }

    public function isMorning(): bool
    {
        return $this->hour < 12;
    }

    public function isAfternoon(): bool
    {
        return $this->hour > 12;
    }

    public function isLeapYear(): bool
    {
        return checkdate(2, 29, $this->year);
    }

    public function getDaysInMonth(): int
    {
        return match($this->month) {
            self::JANUARY, self::MARCH, self::MAY, self::JULY, self::AUGUST, self::OCTOBER, self::DECEMBER => 31,
            self::FEBRUARY => $this->isLeapYear ? 29 : 28,
            self::APRIL, self::JUNE, self::SEPTEMBER, self::NOVEMBER => 30
        };
    }

    public function isMonday(): bool
    {
        return $this->dayOfWeek === self::MONDAY;
    }

    public function isTuesday(): bool
    {
        return $this->dayOfWeek === self::TUESDAY;
    }

    public function isWednesday(): bool
    {
        return $this->dayOfWeek === self::WEDNESDAY;
    }

    public function isThursday(): bool
    {
        return $this->dayOfWeek === self::THURSDAY;
    }

    public function isFriday(): bool
    {
        return $this->dayOfWeek === self::FRIDAY;
    }

    public function isSaturday(): bool
    {
        return $this->dayOfWeek === self::SATURDAY;
    }

    public function isSunday(): bool
    {
        return $this->dayOfWeek === self::SUNDAY;
    }

    public function goForwardDays(int $days): self
    {
        $this->addDays($days);
        return $this;
    }

    public function goBackDays(int $days): self
    {
        $this->subDays($days);
        return $this;
    }

    public function goToTomorrow(): self
    {
        return $this->goForwardDays(1);
    }

    public function goToYesterday(): self
    {
        return $this->goBackDays(1);
    }

    public function goForwardWorkingDays(int $days): self
    {
        for($i = 0; $i < $days; $i++) {
            $this->goToTomorrow();
            while ($this->isWeekend) {
                $this->goToTomorrow();
            }
        }

        return $this;
    }

    public function goBackWorkingDays(int $days): self
    {
        for($i = 0; $i < $days; $i++) {
            $this->goToYesterday();
            while ($this->isWeekend) {
                $this->goToYesterday();
            }
        }

        return $this;
    }

    public function goForwardToDayOfWeek(int $dayOfWeek): self
    {
        if ($dayOfWeek < self::SUNDAY || $dayOfWeek > self::SATURDAY) {
            return $this;
        }

        while ($this->dayOfWeek !== $dayOfWeek) {
            $this->goToTomorrow();
        }

        return $this;
    }


    public function goBackToDayDayOfWeek(int $dayOfWeek): self
    {
        if ($dayOfWeek < self::SUNDAY || $dayOfWeek > self::SATURDAY) {
            return $this;
        }

        while ($this->dayOfWeek !== $dayOfWeek) {
            $this->goToYesterday();
        }

        return $this;
    }

    public function goToFirstDayInMonth(int|null $dayOfWeek = null): self
    {
        $this->day = 1;

        if (is_null($dayOfWeek) || $dayOfWeek < self::SUNDAY || $dayOfWeek > self::SATURDAY) {
            return $this;
        }

        while ($this->dayOfWeek !== $dayOfWeek) {
            $this->goToTomorrow();
        }

        return $this;
    }

    public function goToSecondDayInMonth(int|null $dayOfWeek = null): self
    {
        $this->goToFirstDayInMonth($dayOfWeek);
        $this->goForwardToDayOfWeek($dayOfWeek);
    }

    public function goToThirdDayInMonth(int|null $dayOfWeek = null): self
    {
        $this->goToSecondDayInMonth($dayOfWeek);
        $this->goForwardToDayOfWeek($dayOfWeek);
    }

    public function goToFourthDayInMonth(int|null $dayOfWeek = null): self
    {
        $this->goToThirdDayInMonth($dayOfWeek);
        $this->goForwardToDayOfWeek($dayOfWeek);
    }

    public function goToLastDayInMonth(int|null $dayOfWeek = null): self
    {
        $this->setDay($this->daysInMonth);

        if (is_null($dayOfWeek) || $dayOfWeek < self::SUNDAY || $dayOfWeek > self::SATURDAY) {
            return $this;
        }

        while ($this->dayOfWeek !== $dayOfWeek) {
            $this->goToTomorrow();
        }

        return $this;
    }

    public function goToFirstWorkingDayInMonth(): self
    {
        $this->day = 1;

        while (!$this->isWeekday) {
            $this->goToTomorrow();
        }

        return $this;
    }

    public function goToLastWorkingDayInMonth(): self
    {
        $this->setDay($this->daysInMonth);

        while (!$this->isWeekday) {
            $this->goToYesterday();
        }

        return $this;
    }

    public function __get(string $name): mixed
    {
        return match($name) {
            'year' => $this->getYear(),
            'month' => $this->getMonth(),
            'day' => $this->getDay(),
            'hour' => $this->getHour(),
            'minute' => $this->getMinute(),
            'second' => $this->getSecond(),
            'microsecond' => $this->getMicrosecond(),
            'dayOfWeek' => $this->getDayOfWeek(),
            'isWeekend' => $this->isWeekend(),
            'isWeekday' => $this->isWeekday(),
            'timestamp' => $this->getTimestamp(),
            'isInTheFuture' => $this->isInTheFuture(),
            'isInThePast' => $this->isInThePast(),
            'isToday' => $this->isToday(),
            'isTomorrow' => $this->isTomorrow(),
            'isYesterday' => $this->isYesterday(),
            'isMorning' => $this->isMorning(),
            'isAfternoon' => $this->isAfternoon(),
            'isLeapYear' => $this->isLeapYear(),
            'daysInMonth' => $this->getDaysInMonth(),
            'isMonday' => $this->isMonday(),
            'isTuesday' => $this->isTuesday(),
            'isWednesday' => $this->isWednesday(),
            'isThursday' => $this->isThursday(),
            'isFriday' => $this->isFriday(),
            'isSaturday' => $this->isSaturday(),
            'isSunday' => $this->isSunday(),
            default => null
        };
    }

    public function __set(string $name, $value): void
    {
        match($name) {
            'year' => $this->setYear(intval($value)),
            'month' => $this->setMonth(intval($value)),
            'day' => $this->setDay(intval($value)),
            'hour' => $this->setHour(intval($value)),
            'minute' => $this->setMinute(intval($value)),
            'second' => $this->setSecond(intval($value)),
            'microsecond' => $this->setMicrosecond(intval($value)),
        };
    }

    public function __toString(): string
    {
        return $this->toString();
    }

    public function toString(): string
    {
        return $this->format($this->defaultFormat);
    }

    public function toIso8601String(): string
    {
        return $this->format(self::FORMAT_ISO_8601_DATETIME);
    }

    public function toRfc822String(): string
    {
        return $this->format(self::FORMAT_RFC_822_DATETIME);
    }

    public function toRfc850String(): string
    {
        return $this->format(self::FORMAT_RFC_850_DATETIME);
    }

    public function toRfc1036String(): string
    {
        return $this->format(self::FORMAT_RFC_1036_DATETIME);
    }

    public function toRfc1123String(): string
    {
        return $this->format(self::FORMAT_RFC_1123_DATETIME);
    }

    public function toRfc2822String(): string
    {
        return $this->format(self::FORMAT_RFC_2822_DATETIME);
    }

    public function toRfc3339String(): string
    {
        return $this->format(self::FORMAT_RFC_3339_DATETIME);
    }

    public function toRfc7231String(): string
    {
        return $this->format(self::FORMAT_RFC_7231_DATETIME);
    }

    public function toRssString(): string
    {
        return $this->format(self::FORMAT_RSS_DATETIME);
    }

    public function toW3cString(): string
    {
        return $this->format(self::FORMAT_W3C_DATETIME);
    }

    public function toAtomString(): string
    {
        return $this->format(self::FORMAT_ATOM);
    }

    public function fromTimestamp(int $timestamp): static
    {
        return static::createFromFormat(self::FORMAT_DATETIME, date(self::FORMAT_DATETIME, $timestamp));
    }

    public static function now(): static
    {
        return new static();
    }

    public static function createFormatFromDateString(ToString|string $date): Str
    {
        if ($date instanceof ToString) {
            $date = $date->toString();
        }

        $pattern = $date;
        $pattern = preg_replace("/(\d{4}-\d{2}-\d{2})/i", "Y-m-d", $pattern);
        $pattern = preg_replace("/(Monday|Tuesday|Wednesday|Thursday|Friday|Saturday|Sunday)/i", "l", $pattern);
        $pattern = preg_replace("/(Mon|Tue|Wed|Thu|Fri|Sat|Sun)/i", "D", $pattern);
        $pattern = preg_replace("/(st|nd|rd|th)/i", "S", $pattern);
        $pattern = preg_replace("/(January|February|April|May|June|July|August|September|October|November|December)/i", "F", $pattern);
        $pattern = preg_replace("/(Jan|Feb|Apr|May|Jun|Jul|Aug|Sep|Oct|Nov|Dec)/i", "M", $pattern);

        if (preg_match("/(\d{4})/", $pattern)){
            $pattern = preg_replace("/(\d{4})/i", "Y", $pattern);
        }

        $matches = array();
        if (preg_match("/(am|pm)/i", $pattern, $matches)){
            $replacement = "a";
            if (in_array($matches[0], array("AM", "PM"))) $replacement = "A";
            $pattern = preg_replace("/(am|pm)/i", $replacement, $pattern);

            $pattern = preg_replace("/(\d{2}:\d{2}:\d{2})/i", "h:i:s", $pattern);
            $pattern = preg_replace("/(\d:\d{2}:\d{2})/i", "g:i:s", $pattern);
            $pattern = preg_replace("/(\d{2}:\d{2})/i", "h:i", $pattern);
            $pattern = preg_replace("/(\d:\d{2})/i", "g:i", $pattern);

        }else{
            $pattern = preg_replace("/(\d{2}:\d{2}:\d{2})/i", "H:i:s", $pattern);
            $pattern = preg_replace("/(\d:\d{2}:\d{2})/i", "G:i:s", $pattern);
            $pattern = preg_replace("/(\d{2}:\d{2})/i", "H:i", $pattern);
            $pattern = preg_replace("/(\d:\d{2})/i", "G:i", $pattern);
        }

        $matches = array();
        if (preg_match("/(\d{1,2})(S?) (M|F)( (Y|\d{2}))/", $pattern, $matches)){
            $day = "d";
            $year = "y";
            if ($matches[2] == "S") $day = "j";
            if ($matches[5] == "Y") $year = "Y";
            $pattern = preg_replace("/(\d{1,2})(S?) (M|F)( (Y|\d{2}))/", "{$day}{$matches[2]} {$matches[3]} {$year}", $pattern);
        }elseif (preg_match("/(\d{1,2})(S?) (M|F)/", $pattern, $matches)){
            $day = "d";
            if ($matches[2] == "S") $day = "j";
            $pattern = preg_replace("/(\d{1,2})(S?) (M|F)/", "{$day}{$matches[2]} {$matches[3]}", $pattern);
        }elseif (preg_match("/(M|F) (\d{1,2}),( (Y|\d{2}))/", $pattern, $matches)){
            $day = "d";
            $year = "y";
            if ($matches[2] == "S") $day = "j";
            if ($matches[4] == "Y") $year = "Y";
            $pattern = preg_replace("/(M|F) (\d{1,2})(S?),( (Y|\d{2}))/", "{$matches[1]} {$day}, {$year}", $pattern);
        }elseif(preg_match("/(\d{1,2})([-.\/])(\d{1,2})([-.\/])(Y)/i", $pattern, $matches)){
            $pattern = preg_replace("/(\d{1,2})([-.\/])(\d{1,2})([-.\/])(Y)/i", "d{$matches[2]}m{$matches[4]}{$matches[5]}", $pattern);
        }elseif(preg_match("/(Y)([-.\/])(\d{1,2})([-.\/])(\d{1,2})/i", $pattern, $matches)){
            $pattern = preg_replace("/(Y)([-.\/])(\d{1,2})([-.\/])(\d{1,2})/i", "{$matches[1]}{$matches[2]}m{$matches[4]}d", $pattern);
        }elseif(preg_match("/(\d{1,2})([-.\/])(\d{1,2})([-.\/])(\d{2})/i", $pattern, $matches)){
            $pattern = preg_replace("/(\d{1,2})([-.\/])(\d{1,2})([-.\/])(\d{2})/i", "d{$matches[2]}m{$matches[4]}y", $pattern);
        }

        return Str::fromString($pattern);
    }

    public static function fromString(string $string): static
    {
        $dt = static::createFromFormat(
            self::createFormatFromDateString($string),
            $string
        );

        if ($dt === false) {
            // @todo Throw exception
        }

        return $dt;
    }
}
