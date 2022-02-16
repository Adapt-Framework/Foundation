<?php

namespace Tests\Adapt\Foundation\Collections;

use Adapt\Foundation\Collections\Collection;
use PHPUnit\Framework\TestCase;

class CollectionTest extends TestCase
{
    public function testAll(): void
    {
        $array = [1, 2, 3];
        $collection = Collection::fromArray($array);
        $this->assertEquals($array, $collection->all());
    }

    public function testAverage(): void
    {
        $array = [
            ['a' => 10, 'b' => 1],
            ['a' => 10, 'b' => 1],
            ['a' => 20, 'b' => 2],
            ['a' => 40, 'b' => 4],
        ];

        $collection = Collection::fromArray($array);
        $this->assertEquals(20, $collection->average('a'));
        $this->assertEquals(2, $collection->average('b'));

        $array = [10, 10, 20, 40];
        $this->assertEquals(20, Collection::fromArray($array)->average());
    }

    public function testChunkWhile(): void
    {
        $array = ['A', 'A', 'B', 'B', 'C', 'C', 'C', 'D'];
        $collection = Collection::fromArray($array);
        $chunks = $collection->chunkWhile(function ($value, $key, $chunk) {
            return $value === $chunk->last();
        });

        $this->assertInstanceOf(Collection::class, $chunks);
        $this->assertEquals(
            [
                [0 => 'A', 1 => 'A'],
                [2 => 'B', 3 => 'B'],
                [4 => 'C', 5 => 'C', 6 => 'C'],
                [7 => 'D']
            ],
            $chunks->toArray()
        );
    }

    public function testCollapse(): void
    {
        $array = [
            [1, 2, 3],
            [4, 5, 6],
            [7, 8, 9]
        ];
        $collection = Collection::fromArray($array);
        $this->assertEquals(
            [1, 2, 3, 4, 5, 6, 7, 8, 9],
            $collection->collapse()->toArray()
        );
    }

    public function testCollect(): void
    {
        $array = [1, 2, 3];
        $collection1 = Collection::fromArray($array);
        $collection2 = $collection1->collect();

        $this->assertEquals($array, $collection1->asArray());
        $this->assertEquals($array, $collection2->asArray());

        $collection2[0] = 'foo';
        $this->assertNotEquals(
            $collection1->asArray(),
            $collection2->asArray()
        );
    }

    public function testContains(): void
    {
        $array = ['a', 'b', 'c'];
        $collection = Collection::fromArray($array);
        $this->assertTrue($collection->contains('b'));
        $this->assertFalse($collection->contains('d'));
        $this->assertTrue(
            $collection->contains(function($value, $item) {
                return $value === 'c';
            })
        );
        $this->assertFalse(
            $collection->contains(function($value, $item) {
                return $value === 'e';
            })
        );
    }

    public function testCountBy(): void
    {
        $array = [1, 2, 2, 3, 3, 3, 4];
        $collection = Collection::fromArray($array);
        $this->assertEquals(
            [1 => 1, 2 => 2, 3 => 3, 4 => 1],
            $collection->countBy()->asArray()
        );

        $array = ['matt@gmail.com', 'james@yahoo.com', 'mary@hotmail.com'];
        $collection = Collection::fromArray($array);
        $counts = $collection->countBy(
            function($email) {
                return substr(strchr($email, "@"), 1);
            }
        );
        $this->assertEquals(
            ['gmail.com' => 1, 'yahoo.com' => 1, 'hotmail.com' => 1],
            $counts->asArray()
        );
    }

    public function testCrossJoin(): void
    {
        $collection = Collection::fromArray([1, 2])
            ->crossJoin(['a', 'b']);

        $this->assertEquals(
            [
                [1, 'a'],
                [1, 'b'],
                [2, 'a'],
                [2, 'b']
            ],
            $collection->toArray()
        );
    }

    public function testDoesntContain(): void
    {
        $this->assertFalse(
            Collection::fromArray([1, 2, 3, 4, 5])
                ->doesntContain(function ($value, $key) {
                    return $value < 5;
                })
        );
    }

    public function testDuplicates(): void
    {
        $this->assertEquals(
            [2 => 'a', 4 => 'b'],
            Collection::fromArray(['a', 'b', 'a', 'c', 'b'])
                ->duplicates()
                ->asArray()
        );

        $this->assertEquals(
            [2 => 'Developer'],
            Collection::fromArray([
                ['email' => 'abigail@example.com', 'position' => 'Developer'],
                ['email' => 'james@example.com', 'position' => 'Designer'],
                ['email' => 'victoria@example.com', 'position' => 'Developer'],
            ])->duplicates('position')->toArray()
        );
    }

    public function testEach(): void
    {
        $iterations = 0;
        Collection::fromArray([1, 2, 3])->each(function($value) use (&$iterations) {
            $iterations++;
        });
        $this->assertEquals(3, $iterations);
    }

    public function testEachSpread(): void
    {
        $self = $this;

        Collection::fromArray([['John Doe', 35], ['Jane Doe', 33]])
            ->eachSpread(
                function($name, $age) use ($self) {
                    $self->assertTrue(in_array($name, ['Jane Doe', 'John Doe']));
                    $self->assertTrue(in_array($age, [33, 35]));
                }
            );
    }

    public function testEvery(): void
    {
        $this->assertTrue(
            Collection::fromArray([1, 2, 3, 4])->every(
                function ($value, $key) {
                    return $value > 0;
                }
            )
        );

        $this->assertFalse(
            Collection::fromArray([1, 2, 3, 4])->every(
                function ($value, $key) {
                    return $value > 2;
                }
            )
        );
    }

    public function testExcept(): void
    {
        $this->assertEquals(
            ['product_id' => 1],
            Collection::fromArray(
                ['product_id' => 1, 'price' => 100, 'discount' => false]
            )->except(['price', 'discount'])
            ->toArray()
        );
    }

    public function testFirstWhere(): void
    {
        $this->assertEquals(
            ['name' => 'Linda', 'age' => 14],
            Collection::fromArray(
                [
                    ['name' => 'Regena', 'age' => null],
                    ['name' => 'Linda', 'age' => 14],
                    ['name' => 'Diego', 'age' => 23],
                    ['name' => 'Linda', 'age' => 84],
                ]
            )->firstWhere('name', 'Linda')
            ->toArray()
        );
    }

    public function testFlatMap(): void
    {
        $this->assertEquals(
            ['name' => 'SALLY', 'school' => 'ARKANSAS', 'age' => '28'],
            Collection::fromArray([
                ['name' => 'Sally'],
                ['school' => 'Arkansas'],
                ['age' => 28]
            ])->flatMap(function ($values) {
                return array_map('strtoupper', $values);
            })->toArray()
        );
    }

    public function testFlatten(): void
    {
        $this->assertEquals(
            [
                ['name' => 'iPhone 6S', 'brand' => 'Apple'],
                ['name' => 'Galaxy S7', 'brand' => 'Samsung'],
            ],
            Collection::fromArray([
                'Apple' => [
                    [
                        'name' => 'iPhone 6S',
                        'brand' => 'Apple'
                    ],
                ],
                'Samsung' => [
                    [
                        'name' => 'Galaxy S7',
                        'brand' => 'Samsung'
                    ],
                ],
            ])->flatten(1)
            ->toArray()
        );
    }

    public function testForget(): void
    {
        $this->assertEquals(
            ['framework' => 'Adapt'],
            Collection::fromArray(
                ['name' => 'Matt', 'framework' => 'Adapt']
            )->forget('name')
            ->toArray()
        );
    }

    public function testGroupBy(): void
    {
        $this->assertEquals(
            [
                'account-x10' => [
                    ['account_id' => 'account-x10', 'product' => 'Chair'],
                    ['account_id' => 'account-x10', 'product' => 'Bookcase'],
                ],
                'account-x11' => [
                    ['account_id' => 'account-x11', 'product' => 'Desk'],
                ],
            ],
            Collection::fromArray([
                ['account_id' => 'account-x10', 'product' => 'Chair'],
                ['account_id' => 'account-x10', 'product' => 'Bookcase'],
                ['account_id' => 'account-x11', 'product' => 'Desk'],
            ])->groupBy('account_id')
            ->toArray()
        );
    }

    public function testHas(): void
    {

    }

    public function testImplode(): void
    {

    }

    public function testIsEmpty(): void
    {

    }

    public function testIsNotEmpty(): void
    {

    }

    public function testJoin(): void
    {

    }

    public function testKeyBy(): void
    {

    }

    public function testMake(): void
    {

    }

    public function testMapInto(): void
    {

    }

    public function testMapSpread(): void
    {

    }

    public function testMapToGroup(): void
    {

    }

    public function testMapWithKeys(): void
    {

    }

    public function testMax(): void
    {

    }

    public function testMedian(): void
    {

    }

    public function testMergeRecursive(): void
    {

    }

    public function testMin(): void
    {

    }

    public function testMode(): void
    {

    }

    public function testNth(): void
    {

    }

    public function testOnly(): void
    {

    }

    public function testPartition(): void
    {

    }

    public function testPipe(): void
    {

    }

    public function testPipeInto(): void
    {

    }

    public function testPipeThrough(): void
    {

    }

    public function testPluck(): void
    {

    }

    public function testPrepend(): void
    {

    }

    public function testPull(): void
    {

    }

    public function testPut(): void
    {

    }

    public function testReduceSpread(): void
    {

    }

    public function testReject(): void
    {

    }

    public function testSliding(): void
    {

    }

    public function testSkip(): void
    {

    }

    public function testSkipUntil(): void
    {

    }

    public function testSkipWhile(): void
    {

    }

    public function testSole(): void
    {

    }

    public function testSort(): void
    {

    }

    public function testSortByKeyAscending(): void
    {

    }

    public function testSortByKeyDescending(): void
    {

    }

    public function testSplit(): void
    {

    }

    public function testTake(): void
    {

    }

    public function testTakeUntil(): void
    {

    }

    public function testTakeWhile(): void
    {

    }

    public function testTap(): void
    {

    }

    public function testTimes(): void
    {

    }

    public function testTransform(): void
    {

    }

    public function testUnion(): void
    {

    }

    public function testWhen(): void
    {

    }

    public function testWhenEmpty(): void
    {

    }

    public function testWhenNotEmpty(): void
    {

    }

    public function testWhere(): void
    {

    }

    public function testWhereBetween(): void
    {

    }

    public function testWhereIn(): void
    {

    }

    public function testWhereInstanceOf(): void
    {

    }

    public function testWhereNotBetween(): void
    {

    }

    public function testWhereNotIn(): void
    {

    }

    public function testWhereNotNull(): void
    {

    }

    public function testWhereNull(): void
    {

    }
}


