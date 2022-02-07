<?php

namespace Adapt\Foundation\Strings;


class Str implements ToString, FromString
{
    protected string $string;

    public function __construct(string $string = '')
    {
        $this->string = $string;
    }

    public static function fromString(string $string): static
    {
        return new static($string);
    }

    public function toString(): string
    {
        return $this->string;
    }

    public function chunkSplit(int $length = 76, ToString|string $separator = "\r\n"): static
    {
        if ($separator instanceof ToString) {
            $separator = $separator->toString();
        }
        return static::fromString(chunk_split($this->string, $length, $separator));
    }

    public function uuencode(): static
    {
        return static::fromString(convert_uuencode($this->string));
    }

    public function uudecode(): static|false
    {
        $string = convert_uudecode($this->string);
        if ($string === false) {
            return false;
        }

        return static::fromString($string);
    }

    public function countChars(int $mode = 0): StringCollection|static
    {
        $output = count_chars($this->string, $mode);
        if (is_array($output)) {
            return StringCollection::fromArray($output);
        }

        return static::fromString($output);
    }

    public function crc32(): int
    {
        return crc32($this->string);
    }

    public function crypt(ToString|string $salt): static
    {
        if ($salt instanceof ToString) {
            $salt = $salt->toString();
        }

        return static::fromString(crypt($this->string, $salt));
    }

    public function explode(ToString|string $separator, int $limit = PHP_INT_MAX): StringCollection
    {
        if ($separator instanceof ToString) {
            $separator = $separator->toString();
        }

        return StringCollection::fromArray(explode($separator, $this->string, $limit));
    }

    public function lowerCaseFirst(): static
    {
        return static::fromString(lcfirst($this->string));
    }

    public function leftTrim(ToString|string $characters = "\n\r\t\v\x00"): static
    {
        if ($characters instanceof ToString) {
            $characters = $characters->toString();
        }

        return static::fromString(ltrim($this->string, $characters));
    }

    public function md5(): static
    {
        return static::fromString(md5($this->string));
    }

    public function metaphone(int $maxPhonemes = 0): static
    {
        return static::fromString(metaphone($this->string, $maxPhonemes));
    }

    public function rightTrim(ToString|string $characters = "\n\r\t\v\x00"): static
    {
        if ($characters instanceof ToString) {
            $characters = $characters->toString();
        }

        return static::fromString(rtrim($this->string, $characters));
    }

    public function sha1(bool $binary = false): static
    {
        return static::fromString(sha1($this->string, $binary));
    }

    public function soundex(): static
    {
        return static::fromString(soundex($this->string));
    }

    public function sprintf(mixed ...$values): static
    {
        $values = array_map(
            function($value) {
                if ($value instanceof ToString) {
                    $value = $value->toString();
                }
                return $value;
            },
            $values
        );

        return static::fromString(sprintf($this->string, ...$values));
    }

    public function sscanf(ToString|string $format, mixed &...$vars): StringCollection|int|null
    {
        $return = sscanf($this->string, $format, ...$vars);
        if (is_array($return)) {
            return StringCollection::fromArray($return);
        }

        return $return;
    }

    public function contains(ToString|string $needle): bool
    {
        if ($needle instanceof ToString) {
            $needle = $needle->toString();
        }

        return str_contains($this->string, $needle);
    }

    public function endsWith(ToString|string $needle): bool
    {
        if ($needle instanceof ToString) {
            $needle = $needle->toString();
        }

        return str_ends_with($this->string, $needle);
    }

    public function getCsv(ToString|string $separator = ',', ToString|string $enclosure = '"', ToString|string $escape = "\\"): StringCollection
    {
        if ($separator instanceof ToString) {
            $separator = $separator->toString();
        }

        if ($enclosure instanceof ToString) {
            $enclosure = $enclosure->toString();
        }

        if ($escape instanceof ToString) {
            $escape = $escape->toString();
        }

        return StringCollection::fromArray(str_getcsv($this->string, $separator, $enclosure, $escape));
    }

    public function replace(ToString|string $search, ToString|string $replace, bool $caseSensitive = true, int &$count = null): static
    {
        if ($search instanceof ToString) {
            $search = $search->toString();
        }

        if ($replace instanceof ToString) {
            $replace = $replace->toString();
        }

        if ($caseSensitive) {
            return static::fromString(str_replace($search, $replace, $this->string, $count));
        }

        return static::fromString(str_ireplace($search, $replace, $this->string, $count));
    }

    public function pad(int $length, ToString|string $padString = ' ', int $padType = STR_PAD_RIGHT): static
    {
        if ($padString instanceof ToString) {
            $padString = $padString->toString();
        }

        return static::fromString(str_pad($this->string, $length, $padString, $padType));
    }

    public static function repeat(ToString|string $string, int $times): static
    {
        if ($string instanceof ToString) {
            $string = $string->toString();
        }

        return static::fromString(str_repeat($string, $times));
    }

    public function rot13(): static
    {
        return static::fromString(str_rot13($this->string));
    }

    public function shuffle(): static
    {
        return static::fromString(str_shuffle($this->string));
    }

    public function split(int $length = 1): StringCollection
    {
        return StringCollection::fromArray(str_split($this->string, $length));
    }

    public function startsWith(ToString|string $needle): bool
    {
        if ($needle instanceof ToString) {
            $needle = $needle->toString();
        }
        return str_starts_with($this->string, $needle);
    }

    public function wordCount(int $format = 0, ToString|string|null $characters = null): StringCollection|int
    {
        if ($characters instanceof ToString) {
            $characters = $characters->toString();
        }

        $output = str_word_count($this->string, $format, $characters);

        if (is_array($output)) {
            return StringCollection::fromArray($output);
        }

        return $output;
    }

    public function position(ToString|string $needle, int $offset = 0, bool $caseSensitive = true): int|false
    {
        if ($needle instanceof ToString) {
            $needle = $needle->toString();
        }

        if ($caseSensitive) {
            return strpos($this->string, $needle, $offset);
        }

        return stripos($this->string, $needle, $offset);
    }

    public function findFirst(ToString|string $needle, bool $beforeNeedle = false, bool $caseSensitive = true): static|false
    {
        if ($needle instanceof ToString) {
            $needle = $needle->toString();
        }
        $output = false;

        if ($caseSensitive) {
            $output = strstr($this->string, $needle, $beforeNeedle);
        } else {
            $output = stristr($this->string, $needle, $beforeNeedle);
        }

        if ($output === false) {
            return false;
        }

        return static::fromString($output);
    }

    public function length(): int
    {
        return strlen($this->string);
    }

    public function reverse(): static
    {
        return static::fromString(strrev($this->string));
    }

    public function lowerCase(): static
    {
        return static::fromString(strtolower($this->string));
    }

    public function upperCase(): static
    {
        return static::fromString(strtoupper($this->string));
    }

    public function translate(ToString|string $from, ToString|string $to): static
    {
        if ($from instanceof ToString) {
            $from = $from->toString();
        }

        if ($to instanceof ToString) {
            $to = $to->toString();
        }

        return static::fromString(strtr($this->string, $from, $to));
    }

    public function substr(int $offset, int|null $length = null): static
    {
        return static::fromString(substr($this->string, $offset, $length));
    }

    public function trim(ToString|string $characters = "\n\r\t\v\x00"): static
    {
        if ($characters instanceof ToString) {
            $characters = $characters->toString();
        }

        return static::fromString(trim($this->string, $characters));
    }

    public function upperCaseFirst(): static
    {
        return static::fromString(ucfirst($this->string));
    }

    public function upperCaseWords(ToString|string $separator = " \t\r\n\f\v"): static
    {
        if ($separator instanceof ToString) {
            $separator = $separator->toString();
        }
        return static::fromString(ucwords($this->string, $separator));
    }

    public function wordWrap(int $width = 75, ToString|string $break = "\n", bool $cutLongWords = false): static
    {
        if ($break instanceof ToString) {
            $break = $break->toString();
        }
        return static::fromString(wordwrap($this->string, $width, $break, $cutLongWords));
    }
}
