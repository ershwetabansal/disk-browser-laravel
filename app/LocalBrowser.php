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
     *
     */
    public function listFilesIn($directory = DIRECTORY_SEPARATOR)
    {
        $fileNames = File::filesIn($this->disk, $directory);
        $files = [];

        foreach( $fileNames as $file) {
            if (File::isFileAllowedOnDisk($file, $this->disk) == true) {
                $fileMetaData = File::getFileMetaData($file, $this->disk);
                if ($fileMetaData != []) {
                    $files[] = $fileMetaData;
                }
            }
        }

        return $files;
    }

    /**
     * List all directories in a given directory
     * @param $path
     * @return array
     */
    public function listDirectoriesIn($path = DIRECTORY_SEPARATOR)
    {
        $directories = Directory::directoriesIn($this->disk, $path);
        $directoriesList = [];

        foreach( $directories as $directory ) {
            $directoriesList[] = Directory::getDirectoryMetaData($directory, $this->disk);
        }

        return $directoriesList;
    }

    /**
     * Create a new directory
     * @param string $path
     * @param string $name
     * @return object
     */
    public function createDirectory($name, $path = DIRECTORY_SEPARATOR)
    {
        $isDirectoryAdded = Directory::createDirectory($name, $this->disk, $path );

        return ($isDirectoryAdded == true)
                ? Directory::getDirectoryMetaData($path . DIRECTORY_SEPARATOR . $name, $this->disk)
                : null;

    }

    /**
     * Create file in a directory
     * @param UploadedFile $file
     * @param string $path
     * @return mixed
     */
    public function createFile(UploadedFile $file, $path = DIRECTORY_SEPARATOR)
    {
        $newFileName = File::generateUniqueFileName($file);

        $isFileUploaded = File::uploadFile($file, $newFileName, $this->disk, $path);

        return ($isFileUploaded == true)
               ? File::getFileMetaData($path . DIRECTORY_SEPARATOR . $newFileName, $this->disk)
               : [];
    }

    /**
     * Search a string in disk matching files' and directories' names
     * @param string $searchedWord
     * @return array
     */
    public function searchDisk($searchedWord)
    {
        return [
            'files' => File::searchDisk($searchedWord, $this->disk),
            'directories' => Directory::searchDisk($searchedWord, $this->disk),
        ];
    }

}
