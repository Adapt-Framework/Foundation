<?php

namespace Tests\Adapt\Foundation\FileSystem;

use Adapt\Foundation\FileSystem\Directory;
use Adapt\Foundation\FileSystem\File;
use PHPUnit\Framework\TestCase;

class DirectoryTest extends TestCase
{
    public function testOffsetGet(): void
    {
        $dir = Directory::fromString('/home/matt');

        foreach($dir as $item) {
            if ($item instanceof File) {
                print 'FILE: ' . $item->path->toString() . "\n";
            } elseif (is_string($item)) {
                print 'STRING: ' . $item . "\n";
            } elseif ($item instanceof Directory) {
                print 'DIRECTORY: ' . $item->path->toString() . "\n";
            }
        }
    }
}
