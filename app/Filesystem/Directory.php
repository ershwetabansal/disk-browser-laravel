<?php

namespace App\Filesystem;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;

class Directory
{
    /**
     * Create a new directory in a given path.
     *
     * @param string $name
     * @param string $path
     * @param string $disk
     * @return boolean
     */
    public static function createDirectory($name, $disk = 'local', $path = '/')
    {
        return Storage::disk($disk)->makeDirectory($path . DIRECTORY_SEPARATOR . $name);
    }

    /**
     * Does the directory already exist?
     * @param string $directoryName
     * @param string $path
     * @param string $disk
     * @return boolean
     */
    public static function doesDirectoryExist($directoryName, $disk, $path = '/')
    {
        var_dump($disk);
        var_dump($path);
        var_dump(Storage::disk($disk)->has($path . DIRECTORY_SEPARATOR . $directoryName));
        return Storage::disk($disk)->has($path . DIRECTORY_SEPARATOR . $directoryName);
    }

    /**
     * Returns all directories in a particular directory
     * @param string $path
     * @param string $disk
     * @return Collection
     */
    public static function directoriesIn($disk, $path = '/')
    {
        return collect(Storage::disk($disk)->directories($path));
    }


}
