<?php

namespace App\Filesystem;

use App\DiskSpecifics;
use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class File
{

    /**
     * @param UploadedFile $file
     * @param string $fileName
     * @param string $disk
     * @param string $directory
     * @return boolean
     */
    public static function uploadFile(UploadedFile $file, $fileName, $disk, $directory = DIRECTORY_SEPARATOR)
    {
       return Storage::disk($disk)->put($directory . DIRECTORY_SEPARATOR . $fileName, file_get_contents($file->getRealPath()));
    }

    /**
     * Returns files in a given path in a given disk
     * @param string $path
     * @param string $disk
     * @return array
     */
    public static function filesIn($disk, $path = DIRECTORY_SEPARATOR)
    {
        return collect(Storage::disk($disk)->files($path));
    }

    /**
     * Returns size of a file
     * @param string $file
     * @param string $disk
     * @return int
     */
    public static function getSizeOf($file, $disk)
    {
        return Storage::disk($disk)->size($file) / 1000;
    }

    /**
     * Returns last modified date of a file in a given disk
     * @param string $file
     * @param string $disk
     * @return string
     */
    public static function getLastModifiedOf($file, $disk)
    {
        return date('Y-m-d H:i:s', Storage::disk($disk)->lastModified($file));
    }

    /**
     * Returns all files in a path in a given disk
     * @param string $disk
     * @param string $path
     * @return array
     */
    public static function allFilesIn($disk, $path = DIRECTORY_SEPARATOR)
    {
        return Storage::disk($disk)->allFiles($path);
    }

    /**
     * Get file meta data to be sent as response
     * @param string $file
     * @param string $disk
     * @return array
     */
    public static function getFileMetaData($file, $disk)
    {
        $fileData = [];

        $fileName = self::getNameFromPath($file);
        $fileData['name'] = $fileName;
        $fileData['path'] = \App\DiskSpecifics::getPathPrefixFor($disk) . $file;
        $fileData['size'] = self::getSizeOf($file, $disk);
        $fileData['modified_at'] = self::getLastModifiedOf($file, $disk);

        return $fileData;
    }

    /**
     * Generate unique file name for a uploaded file
     * @param UploadedFile $file
     * @return string
     */
    public static function generateUniqueFileName(UploadedFile $file)
    {
        return str_slug(preg_replace('/\\.[^.\\s]{3,4}$/', '', $file->getClientOriginalName()), '_') .
                '_' .
                uniqid() .
                '.' .
                $file->getClientOriginalExtension();
    }

    /**
     * Search files in a given disk
     * @param string $searchedWord
     * @param string $disk
     * @return array
     */
    public static function searchDisk($searchedWord, $disk)
    {
        $files = self::allFilesIn($disk);

        $searchedFiles = [];
        foreach ($files as $file) {
            if (strpos(strtolower(self::getNameFromPath($file)), strtolower($searchedWord)) !== false) {
                $fileMetaData = self::getFileMetaData($file, $disk);

                if ($fileMetaData != []) {
                    $searchedFiles[] = $fileMetaData;
                }
            }
        }

        return $searchedFiles;
    }

    /**
     * Is file allowed on disk by checking the extension
     * @param string $file
     * @param string $disk
     * @return bool
     */
    public static function isFileAllowedOnDisk($file, $disk)
    {
        $fileName = self::getNameFromPath($file);

        if (self::isGivenFileHidden($fileName) == false && self::doesTheFileHasExtension($fileName) == true) {
            $mimeTypes = DiskSpecifics::getAllowedFileMimeTypesFor($disk);

            if (str_contains($mimeTypes, array_reverse(explode('.', $fileName))[0]) == true) {
                return true;
            }
        }

        return false;
    }

    /**
     * Does the given file have an extension
     * @param string $file
     * @return bool
     */
    public static function doesTheFileHasExtension($file)
    {
        $indexOfDotOnFileName = strpos($file,'.');
        return ($indexOfDotOnFileName !== false && $indexOfDotOnFileName !== (strlen($file) -1) && $indexOfDotOnFileName != 0 );
    }

    /**
     * Is the given file hidden file
     * @param string $file
     * @return bool
     */
    public static function isGivenFileHidden($file)
    {
        return substr($file,0,1) == '.';
    }
    /**
     * Returns name from a given path string
     * @param $path
     * @return mixed
     */
    private static function getNameFromPath($path)
    {
        $result = array_reverse(explode(DIRECTORY_SEPARATOR, $path));
        return $result[0];
    }
}