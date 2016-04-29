<?php

namespace App;

use App\Filesystem\Directory;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Model;

class LocalFileBrowser extends Model
{


    /**
     * Returns the directory structure in a local disk folder
     * @param $directory
     * @return array
     */
    public static function directoriesStructure($directory)
    {
        $directories = self::subDirectories($directory);
        $directoryHierarchy = [];

        foreach( $directories as $dir) {

            $dirData = [];
            $dirData['path'] = $dir;
            $dirData['name'] = self::getNameFromPath($dir);
            $dirData['directories'] = self::directoriesStructure($dir);

            $directoryHierarchy[] = $dirData;
        }

        return $directoryHierarchy;
    }

    public static function files($directory)
    {
        $fileNames = self::filesInDirectory($directory);
        $files = [];

        foreach( $fileNames as $file) {

            $fileData = [];
            $fileData['name'] = self::getNameFromPath($file);
            if (substr($fileData['name'],0,1) != '.') {
                $fileData['path'] = $file;
                $fileData['size'] = self::size($file);
                $fileData['last_modified_date'] = self::lastModified($file);

                $files[] = $fileData;
            }
        }

        return $files;
    }

    public static function uploadFile($directory, $fileName, $fileContent)
    {
        Storage::disk('local')->put($directory . "/" . $fileName, $fileContent);
    }
    /**
     * Returns the sub directories in a particular directory
     * @param $directory
     * @return mixed
     */
    public static function subDirectories($directory)
    {
        return collect(Storage::disk('local')->directories($directory));
    }

    /**
     * Returns files in a given directory
     * @param $directory
     * @return mixed
     */
    public static function filesInDirectory($directory)
    {
        return collect(Storage::disk('local')->files($directory));
    }

    /**
     * Returns size of a file
     * @param $file
     * @return mixed
     */
    public static function size($file)
    {
        return Storage::size($file) / 1000;
    }

    /**
     * Returns last modified date of a file
     * @param $file
     * @return mixed
     */
    public static function lastModified($file)
    {
        return date('d M Y H:i:s', Storage::lastModified($file));
    }

    /**
     * Returns name from a given path string
     * @param $path
     * @return mixed
     */
    public static function getNameFromPath($path)
    {
        $result = array_reverse(explode('/', $path));
        return $result[0];
    }

    /**
     * Get File path
     * @param $disk
     * @param $location
     * @return string
     */
    public static function getFilePath($disk, $location)
    {
        $path = '';
        if ($disk == 'assets') {
            $path = '/public/assets' . $location;
        }

        return $path;
    }
}
