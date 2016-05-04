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
            $fileMetaData = File::getFileMetaData($file, $this->disk);
            if ($fileMetaData != []) {
                $files[] = $fileMetaData;
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
    public function createDirectory($name, $path = '/')
    {
        $directoryDetails = null;
        $isDirectoryAdded = Directory::createDirectory($name, $this->disk, $path );

        if ($isDirectoryAdded == true) {
            $directoryDetails = Directory::getDirectoryMetaData($path . '/' . $name, $this->disk);
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
            $fileDetails = File::getFileMetaData($path . DIRECTORY_SEPARATOR . $newFileName, $this->disk);
        }

        return $fileDetails;

    }

    public function searchDisk($searchedWord)
    {
        $files = File::allFilesIn($this->disk);
        $directories = Directory::allDirectoriesIn($this->disk);
        
        $searchedFiles = [];
        foreach ($files as $file) {
            if (strpos(self::getNameFromPath($file), $searchedWord) !== false) {
                var_dump($file);
                $fileMetaData = File::getFileMetaData($file, $this->disk);
                var_dump($fileMetaData);
                if ($fileMetaData != []) {
                    $searchedFiles[] = $fileMetaData;
                }
            }
        }
        
        $searchedDirectories = [];
        foreach ($directories as $directory) {
            if (strpos(self::getNameFromPath($directory), $searchedWord) !== false) {
                $searchedDirectories[] = Directory::getDirectoryMetaData($directory, $this->disk);
            }
        }
        return [
            'files' => $searchedFiles,
            'directories' => $searchedDirectories,
        ];
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
