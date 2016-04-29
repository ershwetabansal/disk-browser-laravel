<?php

namespace App\Filesystem;


use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Storage;
use App\Exceptions\Filesystem\MissingPathException;

class Directory
{
    /**
     * Allow to create a new directory
     * @param $disk
     * @param $directory
     * @param $dirName
     * @return
     */
    public static function createDirectoryIn($disk, $directory, $dirName)
    {
        return Storage::disk($disk)->makeDirectory($directory . "/" . $dirName);
    }

    /**
     * Does directory already exists
     * @param $disk
     * @param $directory
     * @param $dirName
     * @return mixed
     */
    public static function doesDirectoryExistsIn($disk, $directory, $dirName)
    {
        return Storage::disk($disk)->has($directory . "/" . $dirName);
    }

    /**
     * Returns the sub directories in a particular directory
     * @param $disk
     * @param $directory
     * @return mixed
     */
    public static function subDirectoriesIn($disk, $directory)
    {
        return collect(Storage::disk($disk)->directories($directory));
    }


}