<?php

namespace App\Filesystem;

use App\Disks\Disk;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class Path
{

    /**
     * Returns name from a given path string
     * @param string $path
     * @return array
     */
    public static function stripName($path)
    {
        $result = array_reverse(explode(DIRECTORY_SEPARATOR, $path));
        return $result[0];
    }

    /**
     * It returns the valid path from the input string
     * @param string $path
     * @param bool $isFile
     * @return string
     */
    public static function valid($path, $isFilePath = false)
    {

        if ($path == '/') {
            return $path;
        }

        if (strpos($path, DIRECTORY_SEPARATOR) !== 0) {
            $path = DIRECTORY_SEPARATOR . $path;
        }

        if ($isFilePath == false && strpos($path, DIRECTORY_SEPARATOR, (strlen($path) - 1)) !== (strlen($path) - 1)) {
            $path = $path . DIRECTORY_SEPARATOR ;
        }

        return $path;

    }

}