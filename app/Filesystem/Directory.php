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
    public static function createDirectory($name, $disk = 'local', $path = DIRECTORY_SEPARATOR)
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
    public static function doesDirectoryExist($directoryName, $disk, $path = DIRECTORY_SEPARATOR)
    {
        return Storage::disk($disk)->has($path . DIRECTORY_SEPARATOR . $directoryName);
    }

    /**
     * Does the path exist in a given disk
     * @param string $path
     * @param string $disk
     * @return boolean
     */
    public static function doesPathExist($disk, $path = DIRECTORY_SEPARATOR)
    {
        return Storage::disk($disk)->has($path);
    }

    /**
     * Returns all directories in a particular directory
     * @param string $path
     * @param string $disk
     * @return Collection
     */
    public static function directoriesIn($disk, $path = DIRECTORY_SEPARATOR)
    {
        return collect(Storage::disk($disk)->directories($path));
    }

    /**
     * Returns all directories in a given disk
     * @param string $disk
     * @param string $path
     * @return mixed
     */
    public static function allDirectoriesIn($disk, $path = DIRECTORY_SEPARATOR)
    {
        return Storage::disk($disk)->allDirectories($path);
    }

    /**
     * Get directory meta data
     * @param string $directoryPath
     * @param string $disk
     * @return array
     */
    public static function getDirectoryMetaData($directoryPath, $disk)
    {
        $directoryData = [];

        $prefix = \App\DiskSpecifics::getPathPrefixFor($disk);
        $directoryData['name'] = self::getNameFromPath($directoryPath);
        $directoryData['path'] = (strpos($directoryPath, DIRECTORY_SEPARATOR) !== 0) ? $prefix . $directoryPath : $directoryPath;
        return $directoryData;
    }

    /**
     * Search matching directories in a given disk
     * @param string $searchedWord
     * @param string $disk
     * @return array
     */
    public static function searchDisk($searchedWord, $disk)
    {
        $directories = Directory::allDirectoriesIn($disk);
        $searchedDirectories = [];
        foreach ($directories as $directory) {
            if (strpos(strtolower(self::getNameFromPath($directory)), strtolower($searchedWord)) !== false) {
                $searchedDirectories[] = Directory::getDirectoryMetaData($directory, $disk);
            }
        }

        return $searchedDirectories;
    }

    /**
     * Returns name from a given path string
     * @param string $path
     * @return mixed
     */
    private static function getNameFromPath($path)
    {
        $result = array_reverse(explode(DIRECTORY_SEPARATOR, $path));
        return $result[0];
    }

}
