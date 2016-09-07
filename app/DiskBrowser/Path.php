<?php

namespace App\DiskBrowser;

use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class Path
{

    /**
     * Returns name from a given path string.
     *
     * @param string $path
     * @return array
     */
    public static function stripName($path)
    {
        return basename($path);
    }


    /**
     * Get parent directory from full path.
     *
     * @param string $path
     * @return string
     */
    public static function getDirectoryFromFullPath($path)
    {
        return (dirname($path) == '.') ? DIRECTORY_SEPARATOR : dirname($path);
    }

    /**
     * It returns the valid path from the input string.
     *
     * @param string $path
     * @return string
     */
    public static function normalize($path)
    {
        $path = (!starts_with($path, DIRECTORY_SEPARATOR)) ? DIRECTORY_SEPARATOR . $path : $path;

        return (!is_file($path) && !ends_with($path, DIRECTORY_SEPARATOR)) ? $path . DIRECTORY_SEPARATOR : $path;


    }

}