<?php

namespace App\Filesystem;

use Symfony\Component\HttpFoundation\File\UploadedFile;

interface DiskBrowserContract
{
    /**
     * List all files in a given directory.
     *
     * @param string $directory
     * @return array
     */
    public function listFilesIn($directory);

    /**
     * List all directories in a given directory
     * @param $directory
     * @return array
     */
    public function listDirectoriesIn($directory);

    /**
     * Create a new directory
     * @param $directory
     * @param $name
     * @return object
     */
    public function createDirectoryIn($directory, $name);

    /**
     * Create file in a directory
     * @param $directory
     * @param UploadedFile $file
     * @return mixed
     */
    public function createFileIn($directory, UploadedFile $file);

}