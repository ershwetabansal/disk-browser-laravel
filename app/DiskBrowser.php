<?php

namespace App;

use App\Filesystem\Path;
use App\Filesystem\File;
use App\Filesystem\Directory;
use App\Filesystem\DiskBrowserContract;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use App\Exceptions\Filesystem\DirectoryAlreadyExistsException;

class DiskBrowser implements DiskBrowserContract
{

    /**
     * @var string
     */
    private $disk = 'local';

    /**
     * @param string $diskName
     */
    public function __construct($diskName)
    {
        $this->disk = $diskName;
    }

    /**
     * List all files in a given directory.
     *
     * @param string $path
     * @return array
     *
     */
    public function listFilesIn($path = DIRECTORY_SEPARATOR)
    {
        $files = [];
        $path = $path ?: DIRECTORY_SEPARATOR;

        if (Directory::exists($this->disk, $path)) {
            $fileNames = File::filesIn($this->disk, $path);

            foreach( $fileNames as $file) {
                if (File::isFileAllowedOnDisk($file, $this->disk) == true) {
                    $fileMetaData = File::metaDataOf($file, $this->disk);
                    if ($fileMetaData != []) {
                        $files[] = $fileMetaData;
                    }
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
        $directoriesList = [];
        $path = $path ?: DIRECTORY_SEPARATOR;

        if (Directory::exists($this->disk, $path)) {
            $directories = Directory::directoriesIn($this->disk, $path);

            foreach( $directories as $directory ) {
                $directoriesList[] = Directory::metaDataOf($directory, $this->disk);
            }
        }

        return $directoriesList;
    }

    /**
     * @param string $name
     * @param string $path
     * @return array
     * @throws DirectoryAlreadyExistsException
     * @throws Exceptions\Filesystem\PathNotFoundInDiskException
     */
    public function createDirectory($name, $path = DIRECTORY_SEPARATOR)
    {
        $path = $path ?: DIRECTORY_SEPARATOR;
        if (Directory::exists($this->disk, $path) && Directory::notExists($name, $this->disk, $path)) {

            Directory::createDirectory($name, $this->disk, $path );

            return Directory::metaDataOf(Path::valid($path) . $name , $this->disk);
        } else {
            throw new DirectoryAlreadyExistsException();
        }
    }

    /**
     * Create file in a directory
     * @param UploadedFile $file
     * @param string $path
     * @return array
     */
    public function createFile(UploadedFile $file, $path = DIRECTORY_SEPARATOR)
    {
        $path = $path ?: DIRECTORY_SEPARATOR;

        $newFileName = File::generateUniqueFileName($file);

        if (Directory::exists($this->disk, $path)) {
            File::uploadFile($file, $newFileName, $this->disk, $path);
            return File::metaDataOf(Path::valid($path) . $newFileName, $this->disk);
        } else {
            return [];
        }
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

    /**
     * Delete a directory only if directory is empty
     * @param string $directory
     * @return boolean
     * @throws Exceptions\Filesystem\DirectoryIsNotEmptyException
     */
    public function deleteDirectory($directory)
    {
        return Directory::delete($directory, $this->disk);
    }

}
