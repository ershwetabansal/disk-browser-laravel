<?php

namespace App\Filesystem;

use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class File
{

    /**
     * @param UploadedFile $file
     * @param $fileName
     * @param $disk
     * @param string $directory
     */
    public static function uploadFile(UploadedFile $file, $fileName, $disk, $directory = '/')
    {
       return Storage::disk($disk)->put($directory . DIRECTORY_SEPARATOR . $fileName, file_get_contents($file->getRealPath()));
    }


    /**
     * Returns files in a given directory
     * @param $directory
     * @return mixed
     */
    public static function filesIn($disk, $directory)
    {
        return collect(Storage::disk($disk)->files($directory));
    }

    /**
     * Returns size of a file
     * @param $file
     * @param $disk
     * @return mixed
     */
    public static function getSizeOf($file, $disk)
    {
        return Storage::disk($disk)->size($file) / 1000;
    }

    /**
     * Returns last modified date of a file
     * @param $file
     * @param $disk
     * @return mixed
     */
    public static function getLastModifiedOf($file, $disk)
    {
        return date('Y-m-d H:i:s', Storage::disk($disk)->lastModified($file));
    }

    public static function allFilesIn($disk, $path = '/')
    {
        return Storage::disk($disk)->allFiles($path);
    }

    public static function getFileMetaData($file, $disk)
    {
        $fileData = [];

        $fileName = self::getNameFromPath($file);
        if (substr($fileName,0,1) != '.') {
            $fileData['name'] = $fileName;
            $fileData['path'] = \App\DiskSpecifics::getPathPrefixFor($disk) . $file;
            $fileData['size'] = self::getSizeOf($file, $disk);
            $fileData['modified_at'] = self::getLastModifiedOf($file, $disk);
        }

        return $fileData;
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