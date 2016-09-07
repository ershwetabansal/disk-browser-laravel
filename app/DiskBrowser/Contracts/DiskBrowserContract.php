<?php

namespace App\DiskBrowser\Contracts;

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
     * List all directories in a given directory.
     *
     * @param string $directory
     * @return array
     */
    public function listDirectoriesIn($directory);

    /**
     * Create a new directory.
     *
     * @param string $path
     * @param string $name
     * @return object
     */
    public function createDirectory($name, $path);

    /**
     * Create file in a directory.
     *
     * @param UploadedFile $file
     * @param string $path
     * @return mixed
     */
    public function createFile(UploadedFile $file, $path);

    /**
     * Search a string in disk matching files' and directories' names.
     *
     * @param string $keywords
     * @return array
     */
    public function search($keywords);
}