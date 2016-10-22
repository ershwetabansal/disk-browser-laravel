<?php

namespace App\DiskBrowser;


class Disk
{

    /**
     * It should return the path prefix for a given disk.
     *
     * @param string $disk
     * @return string
     */
    public static function pathPrefixFor($disk = 'local')
    {

        $diskData = config('filesystems.disks')[$disk];

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
        $diskData = config('filesystems.disks')[$disk];

        if ($diskData && isset($diskData['allowed_extensions'])) {
            return $diskData['allowed_extensions'];
        }

        return [];
    }

}
