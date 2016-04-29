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
    public function listFilesIn($directory)
    {
        $fileNames = File::filesIn($this->disk, $directory);
        $files = [];
        foreach( $fileNames as $file) {

            $fileData = [];
            $fileData['name'] = self::getNameFromPath($file);
            if (substr($fileData['name'],0,1) != '.') {
                $fileData['path'] = config('filesystems.disks.ea_images.path') . $file;
                $fileData['size'] = File::getSizeOf($file, $this->disk);
                $fileData['last_modified_date'] = File::getLastModifiedOf($file, $this->disk);

                $files[] = $fileData;
            }
        }

        return $files;
    }

    /**
     * List all directories in a given directory
     * @param $directory
     * @return array
     */
    public function listDirectoriesIn($directory)
    {
        $directories = Directory::subDirectoriesIn($this->disk, $directory);
        $directoriesList = [];

        foreach( $directories as $dir ) {

            $dirData = [];
            $dirData['path'] = $dir;
            $dirData['name'] = self::getNameFromPath($dir);

            $directoriesList[] = $dirData;
        }

        return $directoriesList;
    }

    /**
     * Create a new directory
     * @param $directory
     * @param $name
     * @return object
     */
    public function createDirectoryIn($directory, $name)
    {
        $isDirectoryAdded = false;

        if (Directory::doesDirectoryExistsIn($this->disk, $directory, $name) == false) {
            $isDirectoryAdded = Directory::createDirectoryIn($this->disk, $directory, $name);
        }

        return $isDirectoryAdded;
    }

    /**
     * Create file in a directory
     * @param $directory
     * @param UploadedFile $file
     * @return mixed
     */
    public function createFileIn($directory, UploadedFile $file)
    {
        $newFileName = uniqid() . '_' . 
                       str_slug($file->getClientOriginalName(), '_') . '.' . 
                       $file->getClientOriginalExtension();

        $isFileUploaded = File::uploadFile($file, $newFileName, $this->disk, $directory);

        $fileDetails = [];

        if ($isFileUploaded == true) {
            $fileDetails = ['path' => $directory . DIRECTORY_SEPARATOR . $newFileName,
                'name' => $newFileName,
                'size' => File::getSizeOf($directory . DIRECTORY_SEPARATOR . $newFileName, $this->disk),
                'last_modified_date' => File::getLastModifiedOf($directory . DIRECTORY_SEPARATOR . $newFileName, $this->disk),
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
