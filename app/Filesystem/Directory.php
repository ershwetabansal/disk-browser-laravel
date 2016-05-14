<?php

namespace App\Filesystem;

use App\Disks\Disk;
use App\Exceptions\Filesystem\DirectoryAlreadyExistsException;
use App\Exceptions\Filesystem\DirectoryIsNotEmptyException;
use App\Exceptions\Filesystem\PathNotFoundInDiskException;
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
        return Storage::disk($disk)->makeDirectory(Path::valid($path) . $name);
    }

    /**
     * Returns exception if path does not exist in given disk otherwise returns true
     * @param string $disk
     * @param string $path
     * @return boolean
     * @throws PathNotFoundInDiskException
     */
    public static function exists($disk, $path = DIRECTORY_SEPARATOR)
    {
        if ($path != DIRECTORY_SEPARATOR && !Storage::disk($disk)->has($path)) {
            throw new PathNotFoundInDiskException();
        }
        return true;
    }


    /**
     * Returns exception if directory already exists in given disk otherwise returns true
     * @param $name
     * @param $disk
     * @param string $path
     * @return bool
     * @throws DirectoryAlreadyExistsException
     */
    public static function notExists($name, $disk, $path = DIRECTORY_SEPARATOR)
    {
        if (Storage::disk($disk)->has(Path::valid($path) . $name)) {
            throw new DirectoryAlreadyExistsException();
        }
        return true;
    }

    /**
     * Returns all directories in a particular directory
     * @param string $path
     * @param string $disk
     * @return Collection
     */
    public static function directoriesIn($disk, $path = DIRECTORY_SEPARATOR)
    {
        return Storage::disk($disk)->directories($path);
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
    public static function metaDataOf($directory, $disk)
    {
        $directoryData = [];

        $prefix = Disk::pathPrefixFor($disk);
        $directoryName = Path::stripName($directory);
        $directoryPath = substr($directory, 0, strlen($directory) - strlen($directoryName));
        $directoryData['name'] = $directoryName;
        $directoryData['path'] =  Path::valid(($prefix === '/' ? '' : $prefix) . $directoryPath);
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
            if (strpos(strtolower(Path::stripName($directory)), strtolower($searchedWord)) !== false) {
                $searchedDirectories[] = Directory::metaDataOf($directory, $disk);
            }
        }

        return $searchedDirectories;
    }

    /**
     * Deletes a directory in a given disk
     * @param $disk
     * @param $directory
     * @return mixed
     * @throws DirectoryIsNotEmptyException
     */
    public static function delete($directory, $disk)
    {
        if (self::isEmpty($directory, $disk)) {
            return Storage::disk($disk)->deleteDirectory($directory);
        } else {
            throw new DirectoryIsNotEmptyException;
        }

    }

    /**
     * Returns true if a directory is empty
     * @param $directory
     * @param $disk
     * @return bool
     */
    public static function isEmpty($directory, $disk)
    {
        return (sizeof(self::directoriesIn($disk, $directory)) == 0) &&
               (sizeof(File::filesIn($disk, $directory)) == 0);
    }

}
