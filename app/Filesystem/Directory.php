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

    public static function allDirectoriesIn($disk, $path = '/')
    {
        return Storage::disk($disk)->allDirectories($path);
    }

    public static function getDirectoryMetaData($directory, $disk)
    {
        $directoryData = [];

        $prefix = \App\DiskSpecifics::getPathPrefixFor($disk);
        $directoryData['name'] = self::getNameFromPath($directory);
        $directoryData['path'] = (strpos($directory, '/') !== 0) ? $prefix . $directory : $directory;
        return $directoryData;
    }

    /**
     * Returns name from a given path string
     * @param $path
     * @return mixed
     */
    private static function getNameFromPath($path)
    {
        $result = array_reverse(explode('/', $path));
        return $result[0];
    }
}
