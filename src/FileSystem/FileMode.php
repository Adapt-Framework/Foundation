<?php

namespace Adapt\Foundation\FileSystem;

class FileMode
{
    public const READ_ONLY = 'r';
    public const READ_AND_WRITE = 'r+';
    public const WRITE_ONLY = 'w';
    public const WRITE_AND_READ = 'w+';
    public const APPEND_ONLY = 'a';
    public const APPEND_AND_READ = 'a+';
    public const CREATE_AND_WRITE = 'x';
    public const CREATE_AND_WRITE_AND_READ = 'x+';
    public const CREATE_AND_WRITE_FROM_START = 'c';
    public const CREATE_AND_READ_WRITE_FROM_START = 'c+';
    public const SET_CLOSE_ON_EXEC_FLAG = 'e';
    public const TEXT = 't';
    public const BINARY = 'b';
}
