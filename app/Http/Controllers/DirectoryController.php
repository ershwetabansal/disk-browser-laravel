<?php

namespace App\Http\Controllers;


use App\Filesystem\Directory;
use App\Http\Requests\DeleteDirectoryRequest;
use App\LocalBrowser;
use App\Http\Requests;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\GetDirectoriesRequest;
use App\Http\Requests\CreateNewDirectoryRequest;

class DirectoryController extends Controller
{

    /**
     * Return the list of directories
     * @param $request
     * @return array
     */
    public function index(GetDirectoriesRequest $request)
    {

        $browser = new LocalBrowser($request->input('disk'));

        return $browser->listDirectoriesIn($request->input('path'));
    }

    /**
     * Create a new directory in the given directory
     * @param CreateNewDirectoryRequest $request
     * @return array
     */
    public function store(CreateNewDirectoryRequest $request)
    {

        $browser = new LocalBrowser($request->input('disk'));

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
        $browser = new LocalBrowser($request->input('disk'));

        $result = $browser->deleteDirectory($request->input('path'));

        return [
            'success' => $result
        ];
    }


}
