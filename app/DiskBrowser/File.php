<?php

namespace App\DiskBrowser;

use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class File
{

    /**
     * Uploads a file to the disk in a given path.
     *
     * @param UploadedFile $file
     * @param string $fileName
     * @param string $disk
     * @param string $path
     * @return boolean
     */
    public static function uploadFile(UploadedFile $file, $fileName, $disk, $path)
    {
       return Storage::disk($disk)->put(Path::normalize($path) . $fileName, file_get_contents($file->getRealPath()));
    }

    /**
     * Returns files in a given path in a given disk.
     *
     * @param string $path
     * @param string $disk
     * @return array
     */
    public static function filesIn($disk, $path)
    {

        return collect(Storage::disk($disk)->files($path));
    }

    /**
     * Returns size of a file.
     *
     * @param string $file
     * @param string $disk
     * @return int
     */
    public static function size($file, $disk)
    {
        return Storage::disk($disk)->size($file) / 1000;
    }

    /**
     * Returns last modified date of a file in a given disk.
     *
     * @param string $file
     * @param string $disk
     * @param string $format
     * @return string
     */
    public static function lastModified($file, $disk, $format = 'Y-m-d H:i:s')
    {
        return date($format, Storage::disk($disk)->lastModified($file));
    }

    /**
     * Returns all files in a path in a given disk.
     *
     * @param string $disk
     * @param string $path
     * @return array
     */
    public static function allFilesIn($disk, $path)
    {
        return Storage::disk($disk)->allFiles($path);
    }

    /**
     * Get file meta data to be sent as response.
     *
     * @param string $path
     * @param string $disk
     * @return array
     */
    public static function metaDataOf($path, $disk)
    {
        return [
            'name' => Path::stripName($path),
            'path' => Disk::pathPrefixFor($disk) . Path::normalize(Path::getDirectoryFromFullPath($path)),
            'size' => self::size($path, $disk),
            'modified_at' => self::lastModified($path, $disk),
        ];
    }

    /**
     * Generate unique file name for a uploaded file.
     *
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
     * Search files in a given disk.
     *
     * @param string $keyword
     * @param string $disk
     * @return array
     */
    public static function searchDisk($keyword, $disk)
    {
        return collect(self::allFilesIn($disk, DIRECTORY_SEPARATOR))
            ->filter(function ($file) use ($keyword) {
                return str_contains(strtolower(Path::stripName($file)), strtolower($keyword));
            })
            ->map(function ($file) use($disk) {
                return self::metaDataOf($file, $disk);
            })
            ->values()
            ->all();
    }

    /**
     * Is file allowed on disk by checking the extension.
     *
     * @param string $path
     * @param string $disk
     * @return bool
     */
    public static function isFileAllowedOnDisk($path, $disk)
    {
        $fileName = Path::stripName($path);

        return !self::isGivenFileHidden($fileName) && self::doesTheFileHaveExtension($fileName) &&
        (sizeof(Disk::extensionsFor($disk)) == 0  || in_array(self::extension($path), Disk::extensionsFor($disk)));
    }

    /**
     * Does the given file have an extension.
     *
     * @param string $path
     * @return bool
     */
    public static function doesTheFileHaveExtension($path)
    {
        return isset(pathinfo($path)['extension']) && (pathinfo($path)['extension'] != '');
    }

    /**
     * Is the given file hidden file.
     *
     * @param string $file
     * @return bool
     */
    public static function isGivenFileHidden($file)
    {
        return substr($file,0,1) == '.';
    }

    /**
     * Return file extension
     *
     * @param string $path
     * @return mixed
     */
    public static function extension($path)
    {
        return pathinfo($path)['extension'];
    }

}