# Adapt Foundation
Foundation is a collection of classes that make life easier in a number of ways.

Foundation contains classes for making arrays easier, building upon this to offer a
comprehensive collection class.

Foundation contains classes for working with strings, or collections of strings
simple and easy.

Think of foundation as the foundation to a bigger project, a foundation that
offers a clean and easy to use interface.

Some key features covered by the docs include:
- Arrays
- Collections
- Strings
- Json
- Comparisons
- ...

## Arrays
Foundation offers a number of array helper classes that make working with arrays much easier.

Foundation also offers the foundations for building array based classes, classes that can act as an array.

For example:
```php
class MyArray extends \Adapt\Foundation\Arrays\Foundation {
    // ... Add your class code here ...
}

$myArray = MyArray::fromArray(['hello', 'world']);

print $myArray[0] . ' ' . $myArray[1];
```

In the above example, we are able to treat our new class as if it was an array.

This allows us to work with object lists, and customise logic around the object types,
maybe for holding a collection of models that called all be saved at the same time:
```php
class ModelCollection extends \Adapt\Foundation\Arrays\Foundation {
    public function saveAll(): void
    {
        foreach($this as $model) {
            $model->save();
        }
    }
}

$collection = new ModelCollection::fromArray([]);
$collection[] = new Model(1);
$collection[] = new Model(2);
$collection->saveAll();
```

As well as the Foundation class there is also the `Arr` class that works as a replacement
for standard arrays.

It offers a lot of functionality that makes working with arrays much easier.

```php
use Adapt\Foundation\Arrays\Arr;

$array = Arr::fromArray([['name' => 'Matt', 'age' => 42]]);
$array = $array->merge([['name' => 'Fred', 'age' => 52]]);

print 'Total Age:' . $array->column('age')->sum();
```

`Arr` offers many methods that allow you to manipulate the array in many ways,
you can find the full documentation here.

Foundation also provides two interfaces for array based classes that allow the
class to be created from a PHP array `FromArray`, or convert the class to a PHP array, 
`ToArray` or `AsArray`.

Classes that implement `FromArray` will have the static method `fromArray(array $array)` which
will allow the class to be instantiated from a PHP array.

To export to a PHP class, there is `AsArray` and `ToArray`.

Classes that implement `AsArray` will have the method `asArray()` which will return a 
PHP array of the items.

Classes that implement `ToArray` will have the method `toArray()` which will return
a PHP array of the items.  If any of the items implement `ToArray`, they will also be
converted to PHP arrays.  

So `ToArray` is recursive whereas `AsArray` isn't.

## Collections
Building on `Arr` we have the `Collection` class that goes way beyond the basic array 
functionality and allows for complex maniplulation with ease.

