<?php

namespace App;

use App\Filesystem\Directory;
use App\Filesystem\DiskBrowserContract;
use App\Filesystem\File;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class LocalBrowser implements DiskBrowserContract
{

    /**
     * @var string
     */
    private $disk = 'local';

    public function __construct($diskName)
    {
        $this->disk = $diskName;
    }

    /**
     * List all files in a given directory.
     *
     * @param string $directory
     * @return array
     */
    public function listFilesIn($directory = '/')
    {
        $fileNames = File::filesIn($this->disk, $directory);
        $files = [];
        foreach( $fileNames as $file) {

            $fileData = [];
            $fileData['name'] = self::getNameFromPath($file);
            if (substr($fileData['name'],0,1) != '.') {
                $fileData['path'] = DiskSpecifics::getPathPrefixFor($this->disk) . $file;
                $fileData['size'] = File::getSizeOf($file, $this->disk);
                $fileData['modified_at'] = File::getLastModifiedOf($file, $this->disk);

                $files[] = $fileData;
            }
        }

        return $files;
    }

    /**
     * List all directories in a given directory
     * @param $path
     * @return array
     */
    public function listDirectoriesIn($path = '/')
    {
        $directories = Directory::directoriesIn($this->disk, $path);
        $directoriesList = [];

        foreach( $directories as $directory ) {

            $dirData = [];
            $dirData['path'] = $directory;
            $dirData['name'] = self::getNameFromPath($directory);

            $directoriesList[] = $dirData;
        }

        return $directoriesList;
    }

    /**
     * Create a new directory
     * @param string $path
     * @param string $name
     * @return object
     */
    public function createDirectory($name, $path = '/')
    {
        $directoryDetails = null;
        $isDirectoryAdded = Directory::createDirectory($name, $this->disk, $path );

        if ($isDirectoryAdded == true) {
            $directoryDetails = [
                'name' => $name,
                'path' => $path . '/' . $name,
            ];
        }
        return $directoryDetails;
    }

    /**
     * Create file in a directory
     * @param UploadedFile $file
     * @param string $path
     * @return mixed
     */
    public function createFile(UploadedFile $file, $path)
    {
        $newFileName = str_slug(preg_replace('/\\.[^.\\s]{3,4}$/', '', $file->getClientOriginalName()), '_') .
                        '_' .
                        uniqid() .
                        '.' .
                        $file->getClientOriginalExtension();

        $isFileUploaded = File::uploadFile($file, $newFileName, $this->disk, $path);

        $fileDetails = [];

        if ($isFileUploaded == true) {
            $fileDetails = ['path' => $path . DIRECTORY_SEPARATOR . $newFileName,
                'name' => $newFileName,
                'size' => File::getSizeOf($path . DIRECTORY_SEPARATOR . $newFileName, $this->disk),
                'last_modified_date' => File::getLastModifiedOf($path . DIRECTORY_SEPARATOR . $newFileName, $this->disk),
            ];
        }

        return $fileDetails;

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

}
