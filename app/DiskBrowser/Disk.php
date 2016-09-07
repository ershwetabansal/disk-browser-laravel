<?php

namespace App\DiskBrowser;


class Disk
{

    protected static $diskDetails = [
        'documents'    => [
            'path_prefix'           => '/documents' ,
        ],
        'integration_tests' => [
            'path_prefix'           => '/test',
            'allowed_extensions'    => ['jpeg','png','jpg',]
        ],
        'images' => [
            'path_prefix'           => '/images',
        ],
    ];

    /**
     * It should return the path prefix for a given disk.
     *
     * @param string $disk
     * @return string
     */
    public static function pathPrefixFor($disk = 'local')
    {
        $diskData = self::$diskDetails[$disk];

        if ($diskData && isset($diskData['path_prefix'])) {
            return $diskData['path_prefix'];
        }

        return DIRECTORY_SEPARATOR;
    }

    /**
     * It should return the allowed mimeType for a given disk.
     * 
     * @param string $disk
     * @return array
     */
    public static function extensionsFor($disk = 'local')
    {
        $diskData = self::$diskDetails[$disk];

        if ($diskData && isset($diskData['allowed_extensions'])) {
            return $diskData['allowed_extensions'];
        }

        return [];
    }

}
