<?php

namespace App\Http\Controllers\Api;


use App\Http\Requests;
use App\DiskBrowser\DiskBrowser;
use App\Http\Controllers\Controller;
use App\Http\Requests\DiskBrowser\GetDirectoriesRequest;
use App\Http\Requests\DiskBrowser\DeleteDirectoryRequest;
use App\Http\Requests\DiskBrowser\CreateNewDirectoryRequest;

class DirectoryController extends Controller
{

    /**
     * Return the list of directories
     * @param $request
     * @return array
     */
    public function index(GetDirectoriesRequest $request)
    {

        $browser = new DiskBrowser($request->input('disk'));

        return $browser->listDirectoriesIn($request->input('path'));
    }

    /**
     * Create a new directory in the given directory
     * @param CreateNewDirectoryRequest $request
     * @return array
     */
    public function store(CreateNewDirectoryRequest $request)
    {

        $browser = new DiskBrowser($request->input('disk'));

        $directoryDetails = $browser->createDirectory($request->input('name'), $request->input('path'));

        return [
            'success' => ($directoryDetails != null && isset($directoryDetails)),
            'directory' => $directoryDetails,
        ];
    }

    /**
     * Delete a directory if empty
     * @param DeleteDirectoryRequest $request
     * @return array
     */
    public function destroy(DeleteDirectoryRequest $request)
    {
        $browser = new DiskBrowser($request->input('disk'));

        $result = $browser->deleteDirectory( $request->input('name'), $request->input('path'));

        return [
            'success' => $result
        ];
    }


}
