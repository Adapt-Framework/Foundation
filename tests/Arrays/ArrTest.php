<?php

namespace Tests\Adapt\Foundation\Arrays;

use Adapt\Foundation\Arrays\Arr;
use PHPUnit\Framework\TestCase;

class ArrTest extends TestCase
{
    public function testToFromArray(): void
    {
        $array = ['one', 'two', 'three'];
        $object = Arr::fromArray($array);
        $this->assertEquals($array, $object->toArray());

        $array = [
            'one' => 1,
            'two' => 2,
            'three' => 3
        ];
        $object = Arr::fromArray($array);
        $this->assertEquals($array, $object->toArray());
    }

    public function testIsAssoc(): void
    {
        $array = ['one', 'two', 'three'];
        $this->assertFalse(Arr::fromArray($array)->isAssoc());

        $array = [
            'one' => 1,
            'two' => 2,
            'three' => 3
        ];
        $this->assertTrue(Arr::fromArray($array)->isAssoc());

        $array = [
            0 => 0,
            'one' => 1,
            'two' => 2,
            'three' => 3
        ];
        $this->assertTrue(Arr::fromArray($array)->isAssoc());
    }

    public function testKeys(): void
    {
        $array = [
            'one' => 1,
            'two' => 2,
            'three' => 3
        ];

        $arr = Arr::fromArray($array);
        $this->assertEquals(['one', 'two', 'three'], $arr->keys()->toArray());

        $array = ['one', 'two', 'three'];
        $this->assertEquals([0, 1, 2], Arr::fromArray($array)->keys()->toArray());
    }

    public function testUpperCaseKeys(): void
    {
        $array = [
            'one' => 1,
            'two' => 2,
            'three' => 3
        ];

        $arr = Arr::fromArray($array);
        $this->assertEquals(['ONE', 'TWO', 'THREE'], $arr->upperCaseKeys()->keys()->toArray());
    }

    public function testLowerCaseKeys(): void
    {
        $array = [
            'ONE' => 1,
            'TWO' => 2,
            'THREE' => 3
        ];

        $arr = Arr::fromArray($array);
        $this->assertEquals(['one', 'two', 'three'], $arr->lowerCaseKeys()->keys()->toArray());
    }

    public function testChunk(): void
    {
        $array = [
            'one' => 1,
            'two' => 2,
            'three' => 3,
            'four' => 4,
            'five' => 5,
            'six' => 6,
            'seven' => 7,
            'eight' => 8,
            'nine' => 9,
            'ten' => 10
        ];

        $arr = Arr::fromArray($array);

        $test1 = $arr->chunk(2);
        $this->assertEquals([[1, 2], [3, 4], [5, 6], [7, 8], [9, 10]], $test1->toArray());

        $test2 = $arr->chunk(5, true);

        $expected = [
            [
                'one' => 1,
                'two' => 2,
                'three' => 3,
                'four' => 4,
                'five' => 5
            ],
            [
                'six' => 6,
                'seven' => 7,
                'eight' => 8,
                'nine' => 9,
                'ten' => 10
            ]
        ];
        $this->assertEquals($expected, $test2->toArray());
    }

    public function testColumn(): void
    {
        $array = [
            ['name' => 'Test name', 'email' => 'someone@example.com'],
            ['name' => 'Test name 2', 'email' => 'someone-else@example.com']
        ];

        $arr = Arr::fromArray($array);

        $this->assertEquals(['Test name', 'Test name 2'], $arr->column('name')->toArray());

        $this->assertEquals(
            ['Test name' => 'someone@example.com', 'Test name 2' => 'someone-else@example.com'],
            $arr->column('email', 'name')->toArray()
        );
    }

    public function testCombineWithKeys(): void
    {
        $keys = ['one', 'two', 'three'];
        $values = [1, 2, 3];

        $output = Arr::fromArray($values)->combineWithKeys($keys)->asArray();
        $this->assertEquals(['one' => 1, 'two' => 2, 'three' => 3], $output);
    }

    public function testCombineWithValues(): void
    {
        $keys = ['one', 'two', 'three'];
        $values = [1, 2, 3];

        $output = Arr::fromArray($keys)->combineWithValues($values)->asArray();
        $this->assertEquals(['one' => 1, 'two' => 2, 'three' => 3], $output);
    }

    public function testCountValues(): void
    {
        $array = ['one', 'two', 'two', 'three', 'three', 'three'];
        $output = Arr::fromArray($array)->countValues()->toArray();
        $expected = ['one' => 1, 'two' => 2, 'three' => 3];
        $this->assertEquals($expected, $output);
    }

    public function testDiffAssoc(): void
    {
        $array1 = ["a" => "green", "b" => "brown", "c" => "blue", "red"];
        $array2 = ["a" => "green", "yellow", "red"];
        $expected = ['b' => 'brown', 'c' => 'blue', 0 => 'red'];
        $output = Arr::fromArray($array1)->diffAssoc($array2)->toArray();
        $this->assertEquals($expected, $output);
    }

    public function testDiffKeys(): void
    {
        $array1 = ['blue' => 1, 'red' => 2, 'green' => 3, 'purple' => 4];
        $array2 = ['green' => 5, 'yellow' => 7, 'cyan' => 8];
        $expected = ['blue' => 1, 'red' => 2, 'purple' => 4];
        $output = Arr::fromArray($array1)->diffKeys($array2)->toArray();
        $this->assertEquals($expected, $output);
    }

    public function testDiff(): void
    {
        $array1 = ["a" => "green", "red", "blue", "red"];
        $array2 = ["b" => "green", "yellow", "red"];
        $expected = [1 => 'blue'];
        $output = Arr::fromArray($array1)->diff($array2)->toArray();
        $this->assertEquals($expected, $output);
    }

    public function testFillKeys(): void
    {
        $keys = ['foo', 5, 10, 'bar'];
        $expected = ['foo' => 'banana', 5 => 'banana', 10 => 'banana', 'bar' => 'banana'];
        $output = Arr::fromArray($keys)->fillKeys('banana')->toArray();
        $this->assertEquals($expected, $output);
    }

    public function testFill(): void
    {
        $expected1 = ['banana', 'banana', 'banana'];
        $expected2 = [10 => 'banana', 11 => 'banana', 12 => 'banana'];
        $this->assertEquals($expected1, Arr::fill(3, 'banana')->toArray());
        $this->assertEquals($expected2, Arr::fill(3, 'banana', 10)->toArray());
    }

    public function testFilter(): void
    {
        $array = [1, null, 2, null, 3, null, 4];
        $this->assertEquals([0 => 1, 2 => 2, 4 => 3, 6 => 4], Arr::fromArray($array)->filter()->toArray());

        $output = Arr::fromArray($array)->filter(
            function($value, $key) {
                if ($value && $value <= 2) {
                    return true;
                }

                return false;
            }
        )->asArray();
        $expected = [0 => 1, 2 => 2];
        $this->assertEquals($expected, $output);

        $output = Arr::fromArray($array)->filter(
            function($value, $key) {
                if ($value && $key >= 5) {
                    return true;
                }

                return false;
            }
        )->asArray();
        $expected = [6 => 4];
        $this->assertEquals($expected, $output);
    }

    public function testFlip(): void
    {
        $array = [
            'zero' => 0,
            'one' => 1,
            'two' => 2,
            'five' => 5
        ];

        $expected = [
            'zero',
            'one',
            'two',
            5 => 'five'
        ];

        $this->assertEquals($expected, Arr::fromArray($array)->flip()->toArray());
    }

    public function testGet(): void
    {
        $array = [
            'users' => [
                [
                    'username' => 'matt',
                    'password' => 'password',
                    'name' => [
                        'first' => 'Matt',
                        'last' => 'B'
                    ]
                ]
            ]
        ];

        $this->assertEquals('matt', Arr::fromArray($array)->get('users.0.username'));
        $this->assertEquals('Matt', Arr::fromArray($array)->get('users.0.name.first'));
    }

    public function testIntersectAssoc(): void
    {
        $array1 = ["a" => "green", "b" => "brown", "c" => "blue", "red"];
        $array2 = ["a" => "green", "b" => "yellow", "blue", "red"];
        $output = Arr::fromArray($array1)->intersectAssoc($array2)->toArray();
        $expected = ['a' => 'green'];
        $this->assertEquals($expected, $output);
    }

    public function testIntersectKey(): void
    {
        $array1 = ['blue'  => 1, 'red'  => 2, 'green'  => 3, 'purple' => 4];
        $array2 = ['green' => 5, 'blue' => 6, 'yellow' => 7, 'cyan'   => 8];
        $expected = ['blue' => 1, 'green' => 3];
        $output = Arr::fromArray($array1)->intersectKey($array2)->toArray();
        $this->assertEquals($expected, $output);
    }

    public function testIntersect(): void
    {
        $array1 = ["a" => "green", "red", "blue"];
        $array2 = ["b" => "green", "yellow", "red"];
        $expected = ['a' => 'green', 0 => 'red'];
        $output = Arr::fromArray($array1)->intersect($array2)->toArray();
        $this->assertEquals($output, $expected);
    }

    public function testIsList(): void
    {
        $array = [];
        $this->assertTrue(Arr::fromArray($array)->isList());

        $array = ['apple', 2, 3];
        $this->assertTrue(Arr::fromArray($array)->isList());

        $array = [1 => 'apple', 'orange'];
        $this->assertFalse(Arr::fromArray($array)->isList());
    }

    public function testFirst(): void
    {
        $array = ['one' => 1, 'two' => 2, 'three' => 3];
        $this->assertEquals(1, Arr::fromArray($array)->first());
        $this->assertEquals('one', Arr::fromArray($array)->flip()->first());
    }

    public function testLast(): void
    {
        $array = ['one' => 1, 'two' => 2, 'three' => 3];
        $this->assertEquals(3, Arr::fromArray($array)->last());
        $this->assertEquals('three', Arr::fromArray($array)->flip()->last());
    }

    public function testKeyExists(): void
    {
        $array = ['one' => 1, 'two' => 2, 'three' => 3];
        $this->assertTrue(Arr::fromArray($array)->keyExists('one'));
        $this->assertFalse(Arr::fromArray($array)->keyExists('four'));
        $this->assertTrue(Arr::fromArray($array)->flip()->keyExists(1));
        $this->assertFalse(Arr::fromArray($array)->flip()->keyExists(0));
    }

    public function testKeyFirst(): void
    {
        $array = ['one' => 1, 'two' => 2, 'three' => 3];
        $this->assertEquals('one', Arr::fromArray($array)->keyFirst());
    }

    public function testKeyLast(): void
    {
        $array = ['one' => 1, 'two' => 2, 'three' => 3];
        $this->assertEquals('three', Arr::fromArray($array)->keyLast());
    }


    public function testMap(): void
    {
        $array = [1, 2, 3];
        $closure = function($value) {
            return $value + 10;
        };
        $this->assertEquals([11, 12, 13], Arr::fromArray($array)->map($closure)->toArray());
    }

    public function testMerge(): void
    {
        $array1 = [1, 2, 3];
        $array2 = [4, 5, 6];
        $array3 = [7, 8, 9];
        $expected = [1, 2, 3, 4, 5, 6, 7, 8, 9];
        $this->assertEquals($expected, Arr::fromArray($array1)->merge($array2, $array3)->toArray());
    }

    public function testMergeRecursive(): void
    {
        $arr = Arr::fromArray(['product_id' => 1, 'price' => 100])
            ->mergeRecursive(['product_id' => 2, 'price' => 200, 'discount' => false]);

        $this->assertEquals(
            ['product_id' => [1, 2], 'price' => [100, 200], 'discount' => false],
            $arr->asArray()
        );
    }

    public function testPad(): void
    {
        $array = [1, 2, 3];
        $expected = [1, 2, 3, 99, 99];
        $this->assertEquals($expected, Arr::fromArray($array)->pad(5, 99)->toArray());
    }

    public function testPop(): void
    {
        $array = [1, 2, 3];
        $arr = Arr::fromArray($array);
        $this->assertEquals(3, $arr->pop());
        $this->assertEquals([1, 2], $arr->toArray());
    }

    public function testProduct(): void
    {
        $array = [2, 4, 6, 8];
        $this->assertEquals(384, Arr::fromArray($array)->product());
    }

    public function testPush(): void
    {
        $array = [1, 2, 3];
        $arr = Arr::fromArray($array);
        $this->assertEquals(5, $arr->push(4, 5));
        $this->assertEquals([1, 2, 3, 4, 5], $arr->toArray());
    }

    public function testRandKey(): void
    {
        $array = ['one' => 1, 'two' => 2, 'three' => 3];
        $this->assertIsString(Arr::fromArray($array)->randKey());
        $this->assertInstanceOf(Arr::class, Arr::fromArray($array)->randKey(2));
    }

    public function testRand(): void
    {
        $array = ['one' => 1, 'two' => 2, 'three' => 3];
        $this->assertIsInt(Arr::fromArray($array)->rand());
        $this->assertInstanceOf(Arr::class, Arr::fromArray($array)->rand(2));
    }

    public function testReduce(): void
    {
        $sum = function($carry, $value) {
            return $carry + $value;
        };
        $array = [1, 2, 3];
        $this->assertEquals(6, Arr::fromArray($array)->reduce($sum));
    }

    public function testReplace(): void
    {
        $array = ["orange", "banana", "apple", "raspberry"];
        $replacements = [0 => 'pineapple', 3 => 'cherry'];
        $this->assertEquals(
            ['pineapple', 'banana', 'apple', 'cherry'],
            Arr::fromArray($array)->replace($replacements)->toArray()
        );
    }

    public function testReplaceRecursive(): void
    {
        $base = ['citrus' => [ "orange"] , 'berries' => ["blackberry", "raspberry"]];
        $replacements = ['citrus' => ['pineapple'], 'berries' => ['blueberry']];
        $this->assertEquals(
            [
                'citrus' => ['pineapple'],
                'berries' => ['blueberry', 'raspberry']
            ],
            Arr::fromArray($base)->replaceRecursive($replacements)->toArray()
        );
    }

    public function testReverse(): void
    {
        $array = ['one' => 1, 'two' => 2, 'three' => 3];
        $this->assertEquals(['three' => 3, 'two' => 2, 'one' => 1], Arr::fromArray($array)->reverse()->toArray());
    }

    public function testSearch(): void
    {
        $array = [0 => 'blue', 1 => 'red', 2 => 'green', 3 => 'red'];
        $this->assertEquals(2, Arr::fromArray($array)->search('green'));
        $this->assertEquals(0, Arr::fromArray($array)->search('blue'));
        $this->assertFalse(Arr::fromArray($array)->search('purple'));
    }

    public function testShift(): void
    {
        $array = [1, 2, 3];
        $arr = Arr::fromArray($array);
        $this->assertEquals(1, $arr->shift());
        $this->assertEquals([2, 3], $arr->toArray());
    }

    public function testSlice(): void
    {
        $array = [1, 2, 3, 4, 5];
        $this->assertEquals([2, 3], Arr::fromArray($array)->slice(1, 2)->toArray());
    }

    public function testSplice(): void
    {
        $array = [1, 2, 3, 4, 5];
        $arr = Arr::fromArray($array);
        $replacements = [6, 7];
        $arr = $arr->splice(1, 2, $replacements);

        $this->assertEquals(
            [1, 6, 7, 4, 5],
            $arr->toArray()
        );
    }

    public function testSum(): void
    {
        $array = [1, 2, 3];
        $this->assertEquals(6, Arr::fromArray($array)->sum());
    }

    public function testUnique(): void
    {
        $array = [1, 2, 2, 3, 3, 3];
        $this->assertEquals([1, 2, 3], Arr::fromArray($array)->unique()->values()->toArray());
    }

    public function testUnshift(): void
    {
        $array = ['orange', 'banana'];
        $arr = Arr::fromArray($array);
        $arr->unshift(
            'apple', 'raspberry'
        );

        $this->assertEquals(
            ['apple', 'raspberry', 'orange', 'banana'],
            $arr->toArray()
        );
    }

    public function testValues(): void
    {
        $array = [0 => 'blue', 1 => 'red', 2 => 'green', 3 => 'red'];
        $arr = Arr::fromArray($array);
        $this->assertEquals(
            ['blue', 'red', 'green', 'red'],
            $arr->values()->toArray()
        );
    }

    public function testWalk(): void
    {
        $array = [1, 2, 3, 4, 5];
        $counter = 0;
        $lastArg = null;
        $arr = Arr::fromArray($array);

        $arr->walk(
            function($item, $key, $arg1) use (&$counter, &$lastArg) {
                $lastArg = $arg1;
                $counter++;
            },
            'arg'
        );

        $this->assertEquals(5, $counter);
        $this->assertEquals('arg', $lastArg);
    }

    public function testSortAscending(): void
    {
        $array = [5, 4, 3, 2, 1];
        $arr = Arr::fromArray($array);
        $arr = $arr->sortAscending();

        $this->assertEquals(
            [1, 2, 3, 4, 5],
            $arr->toArray()
        );
    }

    public function testSortDescending(): void
    {
        $array = [1, 2, 3, 4, 5];
        $arr = Arr::fromArray($array);
        $arr = $arr->sortDescending();

        $this->assertEquals(
            [5, 4, 3, 2, 1],
            $arr->toArray()
        );
    }

    public function testIn(): void
    {
        $array = ['a', 'b', 'c', 'd', 'e'];
        $arr = Arr::fromArray($array);

        $this->assertTrue($arr->in('a'));
        $this->assertTrue($arr->in('c'));
        $this->assertTrue($arr->in('e'));
        $this->assertFalse($arr->in('x'));
        $this->assertFalse($arr->in('y'));
        $this->assertFalse($arr->in('z'));
    }

    public function testSortKeysAscending(): void
    {
        $array = ['c' => 1, 'b' => 2, 'a' => 3];
        $arr = Arr::fromArray($array);
        $arr->sortKeysAscending();

        $this->assertEquals(
            ['a' => 3, 'b' => 2, 'c' => 1],
            $arr->toArray()
        );
    }

    public function testSortKeysDescending(): void
    {
        $array = ['a' => 3, 'b' => 2, 'c' => 1];
        $arr = Arr::fromArray($array);
        $arr->sortKeysDescending();

        $this->assertEquals(
            ['c' => 1, 'b' => 2, 'a' => 3],
            $arr->toArray()
        );
    }

    public function testSortNaturally(): void
    {
        $array = ["img12.png", "img10.png", "img2.png", "img1.png"];
        $arr = Arr::fromArray($array);

        $arr = $arr->sortNaturally()->values();
        $this->assertEquals(
            ['img1.png', 'img2.png', 'img10.png', 'img12.png'],
            $arr->toArray()
        );
    }

    public function testRange(): void
    {
        $this->assertEquals(
            [1, 2, 3, 4, 5],
            Arr::range(1, 5)->toArray()
        );
    }

    public function testShuffle(): void
    {
        $array = [1, 2, 3, 4, 5];
        $arr = Arr::fromArray($array);
        $arr->shuffle();
        $this->assertNotEquals(
            $array,
            $arr->toArray()
        );

        $this->assertCount(5, $arr);
    }
}
