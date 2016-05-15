<?php

namespace App\Disks;


class Disk
{

    protected static $diskDetails = [
        'local'             => [
            'pathPrefix'    => '',
            'extensions'    => [],
        ],
        'ea_images'         => [
            'pathPrefix'    => '/images/',
            'extensions'    => ['png','jpg','jpeg','bmp','tiff','gif',],
        ],
        'ea_publications'   => [
            'pathPrefix'    => '/publications/',
            'extensions'    => ['doc','docx','pdf','xls','xlsx',]
        ],
        'integration_tests' => [
            'pathPrefix'    => DIRECTORY_SEPARATOR,
            'extensions'    => ['jpeg','png','jpg',]
        ],
    ];


    /**
     * It should return the path prefix for a given disk
     * @param string $disk
     * @return string
     */
    public static function pathPrefixFor($disk = 'local')
    {
        $diskData = self::$diskDetails[$disk];

        $path = DIRECTORY_SEPARATOR;

        if ($diskData && isset($diskData['pathPrefix'])) {
            $path = $diskData['pathPrefix'];
        }

        return $path;
    }

    /**
     * It should return the allowed mimeType for a given disk
     * @param string $disk
     * @return array
     */
    public static function extensionsFor($disk = 'local')
    {
        $diskData = self::$diskDetails[$disk];

        $extensions = '';

        if ($diskData && isset($diskData['extensions'])) {
            $extensions = $diskData['extensions'];
        }

        return $extensions;
    }

}
