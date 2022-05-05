<?php

namespace Tests\Adapt\Foundation\Collections;

use Adapt\Foundation\Collections\Collection;
use Adapt\Foundation\Dates\DateTime;
use Adapt\Foundation\Strings\Str;
use PHPUnit\Framework\TestCase;
use Tests\Adapt\Foundation\Collections\TestClasses\Currency;
use Tests\Adapt\Foundation\Collections\TestClasses\ResourceClass;

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
            ['matt', 'php', 'javascript'],
            Collection::fromArray([
                'name' => 'matt',
                'languages' => [
                    'php', 'javascript'
                ]
            ])->flatten()->toArray()
        );

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
        $collection = Collection::fromArray([
            'name' => 'Matt',
            'age' => 42
        ]);

        $this->assertTrue($collection->has('name'));
        $this->assertTrue($collection->has('age'));
        $this->assertFalse($collection->has('address'));

    }

    public function testImplode(): void
    {
        $collection = Collection::fromArray(['Foo', 'Bar', 'Foobar']);
        $this->assertEquals(
            'Foo|Bar|Foobar',
            $collection->implode('|')->toString()
        );

        $collection = Collection::fromArray([
            ['name' => 'Matt', 'age' => 42],
            ['name' => 'James', 'age' => 30],
            ['name' => 'Luke', 'age' => 35],
        ]);

        $this->assertEquals(
            'Matt, James, Luke',
            $collection->implode('name', ', ')->toString()
        );
    }

    public function testIsEmpty(): void
    {
        $collection = Collection::create();
        $this->assertTrue($collection->isEmpty());
        $collection[] = 'Hello';
        $this->assertFalse($collection->isEmpty());
    }

    public function testIsNotEmpty(): void
    {
        $collection = Collection::create();
        $this->assertFalse($collection->isNotEmpty());
        $collection[] = 'Hello';
        $this->assertTrue($collection->isNotEmpty());
    }

    public function testJoin(): void
    {
        $collection = Collection::fromArray(['a', 'b', 'c']);
        $this->assertEquals('a, b, c', $collection->join(', '));

        $this->assertEquals('a, b and c', $collection->join(', ', ' and '));

        $collection->pop();
        $this->assertEquals('a, b', $collection->join(', '));

        $this->assertEquals('a and b', $collection->join(', ', ' and '));

        $collection->pop();
        $this->assertEquals(
            'a',
            $collection->join(', ')
        );

        $this->assertEquals(
            'a',
            $collection->join(', ', ' and ')
        );

        $collection->pop();
        $this->assertEquals(
            '',
            $collection->join(', ')
        );

        $this->assertEquals(
            '',
            $collection->join(', ', ' and ')
        );
    }

    public function testKeyBy(): void
    {
        $collection = Collection::fromArray([
            ['product_id' => 'prod-100', 'name' => 'Desk'],
            ['product_id' => 'prod-200', 'name' => 'Chair'],
        ]);

        $keyed = $collection->keyBy('product_id');

        $this->assertEquals(
            [
                'prod-100' => ['product_id' => 'prod-100', 'name' => 'Desk'],
                'prod-200' => ['product_id' => 'prod-200', 'name' => 'Chair'],
            ],
            $keyed->asArray()
        );

        $keyed = $collection->keyBy(function ($item) {
            return strtoupper($item['product_id']);
        });

        $this->assertEquals(
            [
                'PROD-100' => ['product_id' => 'prod-100', 'name' => 'Desk'],
                'PROD-200' => ['product_id' => 'prod-200', 'name' => 'Chair'],
            ],
            $keyed->asArray()
        );
    }

    public function testMake(): void
    {
        $collection = Collection::make(function () {
            return [1, 2, 3];
        });

        $this->assertCount(3, $collection);
    }

    public function testMapInto(): void
    {
        $collection = Collection::fromArray(['GBP', 'EUR', 'USD'])->mapInto(Currency::class);
        $this->assertCount(3, $collection);
        $this->assertInstanceOf(Currency::class, $collection[0]);
        $this->assertEquals('GBP', $collection[0]->code);
        $this->assertInstanceOf(Currency::class, $collection[1]);
        $this->assertEquals('EUR', $collection[1]->code);
        $this->assertInstanceOf(Currency::class, $collection[2]);
        $this->assertEquals('USD', $collection[2]->code);
    }

    public function testMapSpread(): void
    {
        $collection = Collection::fromArray([0, 1, 2, 3, 4, 5, 6, 7, 8, 9])
            ->chunk(2)
            ->mapSpread(function ($odd, $even) {
                return $odd + $even;
            });

        $this->assertEquals(
            [1, 5, 9, 13, 17],
            $collection->asArray()
        );
    }

    public function testMapToGroup(): void
    {
        $collection = Collection::fromArray([
            [
                'name' => 'John Doe',
                'department' => 'Sales',
            ],
            [
                'name' => 'Jane Doe',
                'department' => 'Sales',
            ],
            [
                'name' => 'Johnny Doe',
                'department' => 'Marketing',
            ]
        ])->mapToGroup(function ($item, $key) {
            return [$item['department'] => $item['name']];
        });

        $this->assertEquals([
            'Sales' => ['John Doe', 'Jane Doe'],
            'Marketing' => ['Johnny Doe'],
        ], $collection->asArray());
    }

    public function testMapWithKeys(): void
    {
        $collection = Collection::fromArray([
            [
                'name' => 'John',
                'department' => 'Sales',
                'email' => 'john@example.com',
            ],
            [
                'name' => 'Jane',
                'department' => 'Marketing',
                'email' => 'jane@example.com',
            ]
        ])->mapWithKeys(function ($item, $key) {
            return [$item['email'] => $item['name']];
        });

        $this->assertEquals(
            [
                'john@example.com' => 'John',
                'jane@example.com' => 'Jane',
            ],
            $collection->asArray()
        );
    }

    public function testMax(): void
    {
        $collection = Collection::fromArray([1, 2, 3, 4, 5]);
        $this->assertEquals(5, $collection->max());

        $collection = Collection::fromArray([
            ['foo' => 1, 'bar' => 10],
            ['foo' => 2, 'bar' => 20],
            ['foo' => 3, 'bar' => 30],
            ['foo' => 4, 'bar' => 40],
            ['foo' => 5, 'bar' => 50],
        ]);
        $this->assertEquals(5, $collection->max('foo'));
        $this->assertEquals(50, $collection->max('bar'));
    }

    public function testMedian(): void
    {
        $collection = Collection::fromArray([1, 1, 2, 4]);
        $this->assertEquals(1.5, $collection->median());

        $collection = Collection::fromArray([
            ['foo' => 1, 'bar' => 10],
            ['foo' => 2, 'bar' => 20],
            ['foo' => 3, 'bar' => 30],
            ['foo' => 4, 'bar' => 40],
            ['foo' => 5, 'bar' => 50],
        ]);
        $this->assertEquals(30, $collection->median('bar'));
    }

    public function testMin(): void
    {
        $collection = Collection::fromArray([1, 2, 3, 4, 5]);
        $this->assertEquals(1, $collection->min());

        $collection = Collection::fromArray([
            ['foo' => 1, 'bar' => 10],
            ['foo' => 2, 'bar' => 20],
            ['foo' => 3, 'bar' => 30],
            ['foo' => 4, 'bar' => 40],
            ['foo' => 5, 'bar' => 50],
        ]);
        $this->assertEquals(1, $collection->min('foo'));
        $this->assertEquals(10, $collection->min('bar'));
    }

    public function testMode(): void
    {
        $collection = Collection::fromArray([1, 1, 1, 2, 2, 2, 3]);
        $this->assertEquals([1, 2], $collection->mode());

        $collection = Collection::fromArray([
            ['foo' => 1, 'bar' => 10],
            ['foo' => 1, 'bar' => 10],
            ['foo' => 2, 'bar' => 20],
            ['foo' => 4, 'bar' => 40],
            ['foo' => 5, 'bar' => 50],
        ]);
        $this->assertEquals(1, $collection->mode('foo'));
        $this->assertEquals(10, $collection->mode('bar'));
    }

    public function testNth(): void
    {
        $collection = Collection::fromArray(['a', 'b', 'c', 'd', 'e', 'f']);
        $this->assertEquals(
            ['a', 'e'],
            $collection->nth(4)->asArray()
        );

        $this->assertEquals(
            ['b', 'f'],
            $collection->nth(4, 1)->asArray()
        );
    }

    public function testOnly(): void
    {
        $collection = Collection::fromArray([
            'product_id' => 1,
            'name' => 'Desk',
            'price' => 100,
            'discount' => false
        ])->only(['product_id', 'name']);

        $this->assertEquals(
            ['product_id' => 1, 'name' => 'Desk'],
            $collection->asArray()
        );
    }

    public function testPartition(): void
    {
        [$lower, $upper] = Collection::fromArray([1, 2, 3, 4, 5, 6])
            ->partition(function ($i) {
                return $i < 3;
            });

        $this->assertInstanceOf(Collection::class, $lower);
        $this->assertInstanceOf(Collection::class, $upper);

        $this->assertCount(2, $lower);
        $this->assertCount(4, $upper);

        $this->assertEquals([1, 2], $lower->asArray());
        $this->assertEquals([3, 4, 5, 6], $upper->asArray());
    }

    public function testPipe(): void
    {
        $sum = Collection::fromArray([1, 2, 3])
            ->pipe(function ($collection) {
                return $collection->sum();
            });

        $this->assertEquals(6, $sum);
    }

    public function testPipeInto(): void
    {
        $resource = Collection::fromArray([1, 2, 3])->pipeInto(ResourceClass::class);
        $this->assertInstanceOf(ResourceClass::class, $resource);
        $this->assertInstanceOf(Collection::class, $resource->collection);
        $this->assertEquals(
            [1, 2, 3],
            $resource->collection->asArray()
        );
    }

    public function testPipeThrough(): void
    {
        $mergeFunction = function (Collection $collection) {
            return $collection->merge([4, 5]);
        };

        $sumFunction = function (Collection $collection) {
            return $collection->sum();
        };

        $result = Collection::fromArray([1, 2, 3])->pipeThrough([$mergeFunction, $sumFunction]);
        $this->assertEquals(15, $result);
    }

    public function testPluck(): void
    {
        $collection = Collection::fromArray([
            ['product_id' => 'prod-100', 'name' => 'Desk'],
            ['product_id' => 'prod-200', 'name' => 'Chair'],
        ])->pluck('name');

        $this->assertEquals(
            ['Desk', 'Chair'],
            $collection->asArray()
        );
    }

    public function testPrepend(): void
    {
        $collection = Collection::fromArray([2, 3, 4, 5, 6])->prepend(1);
        $this->assertEquals(
            [1, 2, 3, 4, 5, 6],
            $collection->asArray()
        );
    }

    public function testPull(): void
    {
        $collection = Collection::fromArray(['product_id' => 'prod-100', 'name' => 'Desk']);

        $pulledItem = $collection->pull('name');
        $this->assertEquals('Desk', $pulledItem);
        $this->assertEquals(['product_id' => 'prod-100'], $collection->asArray());
    }

    public function testPut(): void
    {
        $collection = Collection::fromArray(['product_id' => 1, 'name' => 'Desk']);
        $collection->put('price', 100);
        $this->assertEquals(
            ['product_id' => 1, 'name' => 'Desk', 'price' => 100],
            $collection->asArray()
        );
    }

//    public function testReduceSpread(): void
//    {
//        // @todo Check how this is meant to work
//    }

    public function testReject(): void
    {
        $collection = Collection::fromArray([1, 2, 3, 4])
            ->reject(function ($value, $key) {
                return $value > 2;
            });

        $this->assertEquals(
            [1, 2],
            $collection->asArray()
        );
    }

    public function testSliding(): void
    {
        $collection = Collection::fromArray([1, 2, 3, 4, 5])
            ->sliding(2);
        $this->assertEquals(
            [[1, 2], [2, 3], [3, 4], [4, 5]],
            $collection->toArray()
        );

        $collection = Collection::fromArray([1, 2, 3, 4, 5])
            ->sliding(3);
        $this->assertEquals(
            [[1, 2, 3], [2, 3, 4], [3, 4, 5]],
            $collection->toArray()
        );

        $collection = Collection::fromArray([1, 2, 3, 4, 5])
            ->sliding(3, 2);
        $this->assertEquals(
            [[1, 2, 3], [3, 4, 5]],
            $collection->toArray()
        );
    }

    public function testSkip(): void
    {
        $collection = Collection::fromArray([1, 2, 3, 4, 5, 6, 7, 8, 9, 10])
            ->skip(4);
        $this->assertEquals(
            [5, 6, 7, 8, 9, 10],
            $collection->asArray()
        );
    }

    public function testSkipUntil(): void
    {
        $collection = Collection::fromArray([1, 2, 3, 4])
            ->skipUntil(function ($item) {
                return $item >= 3;
            });
        $this->assertEquals(
            [3, 4],
            $collection->asArray()
        );

        $collection = Collection::fromArray([1, 2, 3, 4])
            ->skipUntil(3);
        $this->assertEquals(
            [3, 4],
            $collection->asArray()
        );
    }

    public function testSkipWhile(): void
    {
        $collection = Collection::fromArray([1, 2, 3, 4])
            ->skipWhile(function ($item) {
                return $item <= 3;
            });
        $this->assertEquals(
            [4],
            $collection->asArray()
        );
    }

    public function testSole(): void
    {
        $collection = Collection::fromArray([1, 2, 3, 4]);
        $this->assertEquals(2, $collection->sole(function ($value, $key) {
            return $value === 2;
        }));

        $collection = Collection::fromArray([1, 2, 2, 3, 4]);
        $this->assertFalse($collection->sole(function ($value, $key) {
            return $value === 2;
        }));

        $collection = Collection::fromArray([
            ['product' => 'Desk', 'price' => 200],
            ['product' => 'Chair', 'price' => 100],
        ]);

        $this->assertEquals(
            ['product' => 'Chair', 'price' => 100],
            $collection->sole('product', 'Chair')
        );


    }

    public function testSort(): void
    {
        $collection = Collection::fromArray([10, 9, 8, 7, 6, 5, 4, 3, 2, 1]);
        $this->assertEquals(
            [1, 2, 3, 4, 5, 6, 7, 8, 9, 10],
            $collection->sort()->asArray()
        );

        $collection = Collection::fromArray([1, 2, 3, 4, 5, 6, 7, 8, 9, 10]);
        $collection = $collection->sort(function ($a, $b) {
            if ($a === $b) {
                return 0;
            }

            return ($a > $b) ? -1 : 1;
        });

        $this->assertEquals(
            [10, 9, 8, 7, 6, 5, 4, 3, 2, 1],
            $collection->asArray()
        );
    }

    public function testSortByKeyAscending(): void
    {
        $collection = Collection::fromArray([
            ['name' => 'Desk', 'price' => 200],
            ['name' => 'Chair', 'price' => 100],
            ['name' => 'Bookcase', 'price' => 150],
        ]);

        $collection = $collection->sortByKeyAscending('price');
        $this->assertEquals([
            ['name' => 'Chair', 'price' => 100],
            ['name' => 'Bookcase', 'price' => 150],
            ['name' => 'Desk', 'price' => 200],
        ], $collection->asArray());
    }

    public function testSortByKeyDescending(): void
    {
        $collection = Collection::fromArray([
            ['name' => 'Desk', 'price' => 200],
            ['name' => 'Chair', 'price' => 100],
            ['name' => 'Bookcase', 'price' => 150],
        ]);

        $collection->sortByKeyDescending('name');
        $this->assertEquals([
            ['name' => 'Desk', 'price' => 200],
            ['name' => 'Chair', 'price' => 100],
            ['name' => 'Bookcase', 'price' => 150],
        ], $collection->asArray());
    }

    public function testSplit(): void
    {
        $collection = Collection::fromArray([1, 2, 3, 4, 5])
            ->split(3);
        $this->assertCount(3, $collection);
        $this->assertEquals(
            [[1, 2], [3, 4], [5]],
            $collection->toArray()
        );
    }

    public function testTake(): void
    {
        $collection = Collection::fromArray([0, 1, 2, 3, 4, 5])
            ->take(3);
        $this->assertEquals([0, 1, 2], $collection->asArray());
    }

    public function testTakeUntil(): void
    {
        $collection = Collection::fromArray([1, 2, 3, 4])
            ->takeUntil(function ($item) {
                return $item >= 3;
            });

        $this->assertEquals(
            [1, 2],
            $collection->asArray()
        );
    }

    public function testTakeWhile(): void
    {
        $collection = Collection::fromArray([1, 2, 3, 4])
            ->takeWhile(function ($item) {
                return $item < 3;
            });

        $this->assertEquals(
            [1, 2],
            $collection->asArray()
        );
    }

    public function testTap(): void
    {
        $ascendingCollection = null;

        $descendingCollection = Collection::fromArray([2, 4, 3, 1, 5])
            ->sortAscending()
            ->tap(function(Collection $collection) use (&$ascendingCollection) {
                $ascendingCollection = $collection->collect();
            })
            ->sortDescending();

        $this->assertInstanceOf(Collection::class, $ascendingCollection);
        $this->assertEquals(
            [1, 2, 3, 4, 5],
            $ascendingCollection->asArray()
        );
        $this->assertEquals(
            [5, 4, 3, 2, 1],
            $descendingCollection->asArray()
        );
    }

    public function testTimes(): void
    {
        $collection = Collection::times(10, function ($number) {
            return $number * 9;
        });

        $this->assertEquals(
            [9, 18, 27, 36, 45, 54, 63, 72, 81, 90],
            $collection->asArray()
        );
    }

    public function testTransform(): void
    {
        $collection = Collection::fromArray([1, 2, 3, 4, 5])
            ->transform(function ($item) {
                return $item * 2;
            });

        $this->assertEquals(
            [2, 4, 6, 8, 10],
            $collection->asArray()
        );
    }

//    public function testUnion(): void
//    {
//        $collection = Collection::fromArray([1 => ['a'], 2 => ['b']]);
//        $collection = $collection->union([3 => ['c'], 1 => ['d']]);
//        $this->assertEquals(
//            [1 => ['a'], 2 => ['b'], 3 => ['c']],
//            $collection->toArray()
//        );
//    }

    public function testWhen(): void
    {
        $collection = Collection::fromArray([1, 2, 3]);

        $collection->when(true, function ($collection, $value) {
            $collection->push(4);
            return $collection;
        });

        $this->assertCount(4, $collection);
        $this->assertEquals(4, $collection[3]);

        $collection->when(false, function ($collection, $value) {
            $collection->push(5);
            return $collection;
        });

        $this->assertCount(4, $collection);
    }

    public function testWhenEmpty(): void
    {
        $collection = Collection::create();
        $collection->whenEmpty(function (Collection $collection) {
            $collection->push('Was empty');
            return $collection;
        });

        $this->assertCount(1, $collection);

        $collection->whenEmpty(function (Collection $collection) {
            $collection->push('Was empty');
            return $collection;
        });

        $this->assertCount(1, $collection);
    }

    public function testWhenNotEmpty(): void
    {
        $collection = Collection::create();
        $collection->whenNotEmpty(function (Collection $collection) {
            $collection->push('Was empty');
            return $collection;
        });

        $this->assertCount(0, $collection);
        $collection->push('Hello');

        $collection->whenNotEmpty(function (Collection $collection) {
            $collection->push('Was empty');
            return $collection;
        });

        $this->assertCount(2, $collection);
    }

    public function testWhere(): void
    {
        $collection = Collection::fromArray([
            ['product' => 'Desk', 'price' => 200],
            ['product' => 'Chair', 'price' => 100],
            ['product' => 'Bookcase', 'price' => 150],
            ['product' => 'Door', 'price' => 100],
        ]);

        $filtered = $collection->where('price', 100);
        $this->assertEquals([
            ['product' => 'Chair', 'price' => 100],
            ['product' => 'Door', 'price' => 100],
        ], $filtered->toArray());

        $filtered = $collection->where('price', '==', '100');
        $this->assertEquals([
            ['product' => 'Chair', 'price' => 100],
            ['product' => 'Door', 'price' => 100],
        ], $filtered->toArray());

        $filtered = $collection->where('price', '===', 100);
        $this->assertEquals([
            ['product' => 'Chair', 'price' => 100],
            ['product' => 'Door', 'price' => 100],
        ], $filtered->toArray());

        $filtered = $collection->where('price', '===', '100');
        $this->assertEquals([], $filtered->toArray());

        $filtered = $collection->where('price', '<=', 150);
        $this->assertEquals([
            ['product' => 'Chair', 'price' => 100],
            ['product' => 'Bookcase', 'price' => 150],
            ['product' => 'Door', 'price' => 100],
        ], $filtered->toArray());

        $filtered = $collection->where('price', '<', 150);
        $this->assertEquals([
            ['product' => 'Chair', 'price' => 100],
            ['product' => 'Door', 'price' => 100],
        ], $filtered->toArray());

        $filtered = $collection->where('price', '>=', 150);
        $this->assertEquals([
            ['product' => 'Desk', 'price' => 200],
            ['product' => 'Bookcase', 'price' => 150],
        ], $filtered->toArray());

        $filtered = $collection->where('price', '>', 150);
        $this->assertEquals([
            ['product' => 'Desk', 'price' => 200],
        ], $filtered->toArray());

        $filtered = $collection->where('price', '!=', 150);
        $this->assertEquals([
            ['product' => 'Desk', 'price' => 200],
            ['product' => 'Chair', 'price' => 100],
            ['product' => 'Door', 'price' => 100],
        ], $filtered->toArray());
    }

    public function testWhereBetween(): void
    {
        $collection = Collection::fromArray([
            ['product' => 'Desk', 'price' => 200],
            ['product' => 'Chair', 'price' => 80],
            ['product' => 'Bookcase', 'price' => 150],
            ['product' => 'Pencil', 'price' => 30],
            ['product' => 'Door', 'price' => 100],
        ]);

        $filtered = $collection->whereBetween('price', 100, 200);
        $this->assertEquals([
            ['product' => 'Desk', 'price' => 200],
            ['product' => 'Bookcase', 'price' => 150],
            ['product' => 'Door', 'price' => 100],
        ], $filtered->toArray());
    }

    public function testWhereIn(): void
    {
        $collection = Collection::fromArray([
            ['product' => 'Desk', 'price' => 200],
            ['product' => 'Chair', 'price' => 80],
            ['product' => 'Bookcase', 'price' => 150],
            ['product' => 'Pencil', 'price' => 30],
            ['product' => 'Door', 'price' => 100],
        ]);

        $filtered = $collection->whereIn('price', [80, 30]);
        $this->assertEquals([
            ['product' => 'Chair', 'price' => 80],
            ['product' => 'Pencil', 'price' => 30],
        ], $filtered->toArray());
    }

    public function testWhereInstanceOf(): void
    {
        $collection = Collection::fromArray([
            Str::fromString('Hello'),
            DateTime::now(),
            Str::fromString('World')
        ]);

        $filtered = $collection->whereInstanceOf(Str::class);
        $this->assertCount(2, $filtered);
    }

    public function testWhereNotBetween(): void
    {
        $collection = Collection::fromArray([
            ['product' => 'Desk', 'price' => 200],
            ['product' => 'Chair', 'price' => 80],
            ['product' => 'Bookcase', 'price' => 150],
            ['product' => 'Pencil', 'price' => 30],
            ['product' => 'Door', 'price' => 100],
        ]);

        $filtered = $collection->whereNotBetween('price', 100, 200);
        $this->assertEquals([
            ['product' => 'Chair', 'price' => 80],
            ['product' => 'Pencil', 'price' => 30],
        ], $filtered->toArray());
    }

    public function testWhereNotIn(): void
    {
        $collection = Collection::fromArray([
            ['product' => 'Desk', 'price' => 200],
            ['product' => 'Chair', 'price' => 80],
            ['product' => 'Bookcase', 'price' => 150],
            ['product' => 'Pencil', 'price' => 30],
            ['product' => 'Door', 'price' => 100],
        ]);

        $filtered = $collection->whereNotIn('price', [80, 30]);
        $this->assertEquals([
            ['product' => 'Desk', 'price' => 200],
            ['product' => 'Bookcase', 'price' => 150],
            ['product' => 'Door', 'price' => 100],
        ], $filtered->toArray());
    }

    public function testWhereNotNull(): void
    {
        $collection = Collection::fromArray([
            ['product' => 'Desk', 'price' => 200],
            ['product' => 'Chair', 'price' => 80],
            ['product' => 'Bookcase', 'price' => 150],
            ['product' => 'Pencil', 'price' => null],
            ['product' => 'Door', 'price' => null],
        ]);

        $filtered = $collection->whereNotNUll('price');
        $this->assertEquals([
            ['product' => 'Desk', 'price' => 200],
            ['product' => 'Chair', 'price' => 80],
            ['product' => 'Bookcase', 'price' => 150],
        ], $filtered->toArray());
    }

    public function testWhereNull(): void
    {
        $collection = Collection::fromArray([
            ['product' => 'Desk', 'price' => 200],
            ['product' => 'Chair', 'price' => 80],
            ['product' => 'Bookcase', 'price' => 150],
            ['product' => 'Pencil', 'price' => null],
            ['product' => 'Door', 'price' => null],
        ]);

        $filtered = $collection->whereNull('price');
        $this->assertEquals([
            ['product' => 'Pencil', 'price' => null],
            ['product' => 'Door', 'price' => null],
        ], $filtered->toArray());
    }
}


