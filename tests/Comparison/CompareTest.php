<?php

namespace Tests\Adapt\Foundation\Comparison;

use Adapt\Foundation\Comparison\Compare;
use PHPUnit\Framework\TestCase;

class CompareTest extends TestCase
{
    public function testCompare(): void
    {
        $this->assertTrue(Compare::test(1, Compare::EQUALS, 1));
        $this->assertTrue(Compare::test(1, Compare::EQUALS, '1'));
        $this->assertTrue(Compare::test('hello', Compare::EQUALS, 'hello'));
        $this->assertFalse(Compare::test(1, Compare::EQUALS, 2));

        $this->assertTrue(Compare::test(1, Compare::EQUALS_AND_SAME_TYPE, 1));
        $this->assertFalse(Compare::test(1, Compare::EQUALS_AND_SAME_TYPE, '1'));
        $this->assertTrue(Compare::test('hello', Compare::EQUALS_AND_SAME_TYPE, 'hello'));
        $this->assertFalse(Compare::test(1, Compare::EQUALS_AND_SAME_TYPE, 2));

        $this->assertFalse(Compare::test(1, Compare::GREATER_THAN, 1));
        $this->assertFalse(Compare::test(1, Compare::GREATER_THAN, 2));
        $this->assertTrue(Compare::test(2, Compare::GREATER_THAN, 1));

        $this->assertTrue(Compare::test(1, Compare::GREATER_THAN_OR_EQUALS, 1));
        $this->assertFalse(Compare::test(1, Compare::GREATER_THAN_OR_EQUALS, 2));
        $this->assertTrue(Compare::test(2, Compare::GREATER_THAN_OR_EQUALS, 1));

        $this->assertFalse(Compare::test(1, Compare::LESS_THAN, 1));
        $this->assertTrue(Compare::test(1, Compare::LESS_THAN, 2));
        $this->assertFalse(Compare::test(2, Compare::LESS_THAN, 1));

        $this->assertTrue(Compare::test(1, Compare::LESS_THAN_OR_EQUALS, 1));
        $this->assertTrue(Compare::test(1, Compare::LESS_THAN_OR_EQUALS, 2));
        $this->assertFalse(Compare::test(2, Compare::LESS_THAN_OR_EQUALS, 1));

        $this->assertFalse(Compare::test(1, Compare::NOT_EQUALS, 1));
        $this->assertFalse(Compare::test(1, Compare::NOT_EQUALS, '1'));
        $this->assertFalse(Compare::test('hello', Compare::NOT_EQUALS, 'hello'));
        $this->assertTrue(Compare::test(1, Compare::NOT_EQUALS, 2));

        $this->assertFalse(Compare::test(1, Compare::NOT_EQUALS_OR_NOT_SAME_TYPE, 1));
        $this->assertTrue(Compare::test(1, Compare::NOT_EQUALS_OR_NOT_SAME_TYPE, '1'));
        $this->assertFalse(Compare::test('hello', Compare::NOT_EQUALS_OR_NOT_SAME_TYPE, 'hello'));
        $this->assertTrue(Compare::test(1, Compare::NOT_EQUALS_OR_NOT_SAME_TYPE, 2));
    }
}
