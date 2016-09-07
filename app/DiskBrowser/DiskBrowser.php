<?php

namespace App\DiskBrowser;

use App\DiskBrowser\Contracts\DiskBrowserContract;
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
     */
    public function listFilesIn($path)
    {

        if (Directory::exists($this->disk, $path)) {
            return(collect(File::filesIn($this->disk, $path))
                ->filter(function ($file) {
                    return File::isFileAllowedOnDisk($file, $this->disk);
                })
                ->map(function ($file) {
                    return File::metaDataOf($file, $this->disk);
                })
                ->values()
                ->all());
        }

        return [];
    }

    /**
     * List all directories in a given directory.
     *
     * @param string $path
     * @return array
     */
    public function listDirectoriesIn($path)
    {

        if (Directory::exists($this->disk, $path)) {
            return(collect(Directory::directoriesIn($this->disk, $path))
                ->map(function ($directory) {
                    return  Directory::metaDataOf($directory, $this->disk);
                })
                ->values()
                ->all());
        }

        return [];
    }

    /**
     * Create a directory with a given name.
     *
     * @param string $name
     * @param string $path
     * @return array
     * @throws DirectoryAlreadyExistsException
     */
    public function createDirectory($name, $path)
    {
        if (Directory::exists($this->disk, $path) && Directory::notExists($name, $this->disk, $path)) {
            Directory::createDirectory($name, $this->disk, $path );
            return Directory::metaDataOf(Path::normalize($path) . $name , $this->disk);
        }

        throw new DirectoryAlreadyExistsException();
    }

    /**
     * Create file in a directory.
     *
     * @param UploadedFile $file
     * @param string $path
     * @return array
     */
    public function createFile(UploadedFile $file, $path)
    {
        $newFileName = File::generateUniqueFileName($file);

        if (Directory::exists($this->disk, $path)) {
            File::uploadFile($file, $newFileName, $this->disk, $path);
            return File::metaDataOf(Path::normalize($path) . $newFileName, $this->disk);
        }

        return [];
    }

    /**
     * Search a string in disk matching files' and directories' names.
     *
     * @param string $searchedWord
     * @return array
     */
    public function search($searchedWord)
    {
        return [
            'files' => File::searchDisk($searchedWord, $this->disk),
            'directories' => Directory::searchDisk($searchedWord, $this->disk),
        ];
    }

    /**
     * Delete a directory only if directory is empty.
     *
     * @param string $directory
     * @param string $path
     * @return bool
     */
    public function deleteDirectory($directory, $path)
    {
        return Directory::delete(Path::normalize($path) . $directory, $this->disk);
    }

}
