<?php

namespace App;

use App\Filesystem\Directory;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Model;

class LocalFileBrowser extends Model
{


    public static function directoriesStructure($directory = null)
    {
        if (is_null($directory)) {
            $directory = '/public';
        }

        $directories = self::subDirectories($directory);
        $directoryHierarchy = [];

        foreach( $directories as $dir) {
            $dirData = [];
            $dirData['name'] = $dir;
            $dirData['directories'] = self::directoriesStructure($dir);
            $directoryHierarchy[] = $dirData;
        }

        return $directoryHierarchy;
    }

    public static function subDirectories($directory)
    {
        return collect(Storage::disk('local')->directories($directory));
    }

}
