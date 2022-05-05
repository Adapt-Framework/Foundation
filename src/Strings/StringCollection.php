<?php

namespace Adapt\Foundation\Strings;

use Adapt\Foundation\Arrays\Contracts\AsArray;
use Adapt\Foundation\Arrays\Contracts\ToArray;
use Adapt\Foundation\Collections\Collection;
use Adapt\Foundation\Strings\Contracts\ToString;

class StringCollection extends Collection implements ToString
{
    public function __construct(ToArray|AsArray|array $array = [])
    {
        parent::__construct($array);
        $this->transform(function ($value) {
            if (is_string($value)) {
                return Str::fromString($value);
            }

            if ($value instanceof Str) {
                return $value;
            }

            if ($value instanceof ToString) {
                return Str::fromString($value->toString());
            }

            return null;
        });
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {
        if (is_string($value)) {
            $value = Str::fromString($value);
        }
        parent::offsetSet($offset, $value);
    }

    public function toString(): string
    {
        return $this->implode('')->toString();
    }

    public function __toString(): string
    {
        return $this->toString();
    }

    // Str proxied methods

    public function chunkSplit(int $length = 76, ToString|string $separator = "\r\n"): static
    {
        return $this->map(function (Str $str) use ($length, $separator) {
            return $str->chunkSplit($length, $separator);
        });
    }

    public function uuencode(): static
    {
        return $this->map(function (Str $str) {
            return $str->uuencode();
        });
    }

    public function uudecode(): static
    {
        return $this->map(function (Str $str) {
            return $str->uudecode();
        });
    }

    public function urlencode(): static
    {
        return $this->map(function (Str $str) {
            return $str->urlencode();
        });
    }

    public function urldecode(): static
    {
        return $this->map(function (Str $str) {
            return $str->urldecode();
        });
    }

    public function crc32(): static
    {
        return $this->map(function (Str $str) {
            return $str->crc32();
        });
    }

    public function crypt(ToString|string $salt): static
    {
        return $this->map(function (Str $str) use ($salt) {
            return $str->crypt($salt);
        });
    }

    public function explode(ToString|string $separator, int $limit = PHP_INT_MAX): static
    {
        return $this->map(function (Str $str) use ($separator, $limit) {
            return $str->explode($separator, $limit);
        });
    }

    public function lowerCaseFirst(): static
    {

    }

    public function leftTrim(ToString|string $characters = " \n\r\t\v\x00"): static
    {

    }

    public function md5(): static
    {

    }

    public function metaphone(int $maxPhonemes = 0): static
    {

    }

    public function rightTrim(ToString|string $characters = " \n\r\t\v\x00"): static
    {

    }

    public function sha1(bool $binary = false): static
    {

    }

    public function soundex(): static
    {

    }

    public function sprintf(mixed ...$values): static
    {

    }

    public function sscanf(ToString|string $format, mixed &...$vars): static
    {

    }

    public function endsWith(ToString|string $needle): static
    {

    }

    public function getCsv(
        ToString|string $separator = ',',
        ToString|string $enclosure = '"',
        ToString|string $escape = "\\"
    ): static
    {

    }

    public function strReplace(
        ToString|string $search,
        ToString|string $replace,
        bool            $caseSensitive = true,
        int             &$count = null
    ): static
    {

    }

    public function strPad(int $length, ToString|string $padString = ' ', int $padType = STR_PAD_RIGHT): static
    {
        if ($padString instanceof ToString) {
            $padString = $padString->toString();
        }

        return static::fromString(str_pad($this->string, $length, $padString, $padType));
    }

    public static function repeat(ToString|string $string, int $times): static
    {

    }

    public function rot13(): static
    {

    }

    public function strShuffle(): static
    {

    }

    public function strSplit(int $length = 1): static
    {

    }

    public function startsWith(ToString|string $needle): static
    {

    }

    public function wordCount(int $format = 0, ToString|string|null $characters = null): static
    {

    }


    public function findFirst(
        ToString|string $needle,
        bool            $beforeNeedle = false,
        bool            $caseSensitive = true
    ): static
    {

    }


    public function strReverse(): static
    {

    }

    public function lowerCase(): static
    {

    }

    public function upperCase(): static
    {

    }

    public function translate(ToString|string $from, ToString|string $to): static
    {

    }

    public function substr(int $offset, int|null $length = null): static
    {

    }

    public function trim(ToString|string $characters = " \n\r\t\v\x00"): static
    {

    }

    public function upperCaseFirst(): static
    {

    }

    public function upperCaseWords(ToString|string $separator = " \t\r\n\f\v"): static
    {

    }

    public function wordWrap(int $width = 75, ToString|string $break = "\n", bool $cutLongWords = false): static
    {

    }

    public function escape(): static
    {

    }

    public function unescape(): static
    {

    }
}
