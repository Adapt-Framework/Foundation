# Arrays
This package offers a number of classes that make working with arrays much
easier.

Sometimes you might want to build your own array class, this package offer
a foundation class to make this much easier.

## The `Arr` object
The `Arr` object works in the same way as a traditional PHP array, but offers
much of the functionality in object form.

We can initalise the object from an array of existing objects, or as an empty
array.
```php
use Adapt\Foundation\Arrays\Arr;

$myArray = Arr::create(); // Empty array
$myArray = Arr::fromArray(['one', 'two', 'three']); // With items
```

Once initialised we can use it as a normal array.
```php
use Adapt\Foundation\Arrays\Arr;

$myArray = Arr::create();
$myArray['name'] = 'Matt';
$myArray['age'] = 94;

foreach($myArray as $key => $value) {
    // ...
}
```

`Arr` offers many array methods using a fluid interface which allows us
to easily manipulate arrays.

For example, we can easily access the first key in the array and make it upper
case at the same time.
```php
use Adapt\Foundation\Arrays\Arr;

$myArray = Arr::fromArray([
    'name' => 'Bob',
    'age' => 41,
    'location' => 'London'
]);

print $myArray->upperCaseKeys()->keys()->first(); // prints "NAME"
```

### Available methods
| Method                                           | Returns | Notes                                    |
|--------------------------------------------------|---------|------------------------------------------|
| `isAssoc()`                                      | `bool`  | Returns a `true` if the array is an associative array. |
| `keys()`                                         | `Arr`   | Returns an array of keys |
| `upperCaseKeys()`                                | `Arr`   | Returns an array of keys all in upper case. |
| `lowerCaseKeys()`                                | `Arr` | Returns an array of keys all in lower case. |
| `chunk(int $length, bool $preserveKeys = false)` | `Arr` | Returns an array of arrays of the given chunk size. |


## Building array base objects


