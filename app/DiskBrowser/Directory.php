<?php

namespace App\DiskBrowser;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use App\Exceptions\Filesystem\PathNotFoundInDiskException;
use App\Exceptions\Filesystem\DirectoryIsNotEmptyException;
use App\Exceptions\Filesystem\DirectoryAlreadyExistsException;

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
    public static function createDirectory($name, $disk = 'local', $path)
    {
        return Storage::disk($disk)->makeDirectory(Path::normalize($path) . $name);
    }

    /**
     * Returns exception if path does not exist in given disk otherwise returns true.
     *
     * @param string $disk
     * @param string $path
     * @return boolean
     * @throws PathNotFoundInDiskException
     */
    public static function exists($disk, $path)
    {
        if ($path != DIRECTORY_SEPARATOR && !Storage::disk($disk)->has($path)) {
            throw new PathNotFoundInDiskException();
        }
        return true;
    }

    /**
     * Returns exception if directory already exists in given disk otherwise returns true.
     *
     * @param string $name
     * @param string $disk
     * @param string $path
     * @return bool
     * @throws DirectoryAlreadyExistsException
     */
    public static function notExists($name, $disk, $path)
    {
        if (Storage::disk($disk)->has(Path::normalize($path) . $name)) {
            throw new DirectoryAlreadyExistsException();
        }
        return true;
    }

    /**
     * Returns all directories in a particular directory.
     *
     * @param string $path
     * @param string $disk
     * @return Collection
     */
    public static function directoriesIn($disk, $path)
    {
        return Storage::disk($disk)->directories($path);
    }

    /**
     * Returns all directories in a given disk.
     *
     * @param string $disk
     * @param string $path
     * @return mixed
     */
    public static function allDirectoriesIn($disk, $path)
    {
        return Storage::disk($disk)->allDirectories($path);
    }

    /**
     * Get directory meta data.
     *
     * @param string $directory
     * @return array
     */
    public static function metaDataOf($directory)
    {
        return [
            'name' => Path::stripName($directory),
            'path' => Path::normalize(Path::getDirectoryFromFullPath($directory)),
        ];
    }

    /**
     * Search matching directories in a given disk.
     *
     * @param string $keyword
     * @param string $disk
     * @return array
     */
    public static function searchDisk($keyword, $disk)
    {
        return collect(self::allDirectoriesIn($disk, DIRECTORY_SEPARATOR))
            ->filter(function ($directory) use ($keyword) {
                return str_contains(strtolower(Path::stripName($directory)), strtolower($keyword));
            })
            ->map(function ($directory) {
                return self::metaDataOf($directory);
            })
            ->values()
            ->all();
    }

    /**
     * Deletes a directory in a given disk.
     *
     * @param string $disk
     * @param string $directory
     * @return mixed
     * @throws DirectoryIsNotEmptyException
     */
    public static function delete($directory, $disk)
    {
        if (self::isEmpty($directory, $disk)) {
            return Storage::disk($disk)->deleteDirectory($directory);
        }

        throw new DirectoryIsNotEmptyException;
    }

    /**
     * Returns true if a directory is empty.
     *
     * @param string $directory
     * @param string $disk
     * @return bool
     */
    public static function isEmpty($directory, $disk)
    {
        return (sizeof(self::directoriesIn($disk, $directory)) == 0) &&
               (sizeof(File::filesIn($disk, $directory)) == 0);
    }


}
