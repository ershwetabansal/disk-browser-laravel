<?php

namespace App;


class DiskSpecifics
{

    public static $diskDetails = [
        'local' => [
            'path'       => '',
            'extensions' => [],
        ],
        'ea_images' => [
            'path'       => '/images/',
            'extensions' => 'png,jpg,jpeg,bmp,tiff,gif',
        ],
        'ea_publications' => [
            'path'       => '/publications/',
            'extensions' => 'doc,docx,pdf,xls,xlsx',
        ],
        'integration_tests' => [
            'path'       => '/',
            'extensions' => 'jpeg,png',
        ],
    ];


    /**
     * It should return the path prefix for a given disk
     * @param string $disk
     * @return string
     */
    public static function getPathPrefixFor($disk)
    {
        $diskData = DiskSpecifics::$diskDetails[$disk];

        $path = '/';

        if ($diskData && isset($diskData['path'])) {
            $path = $diskData['path'];
        }

        return $path;
    }

    /**
     * It should return the allowed mimeType for a given disk
     * @param string $disk
     * @return string
     */
    public static function getAllowedFileMimeTypesFor($disk)
    {
        $diskData = DiskSpecifics::$diskDetails[$disk];

        $extensions = '';

        if ($diskData && isset($diskData['extensions'])) {
            $extensions = $diskData['extensions'];
        }

        return $extensions;
    }

}
