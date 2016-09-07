<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests;
use Illuminate\Http\Request;
use App\DiskBrowser\DiskBrowser;
use App\Http\Controllers\Controller;
use App\Http\Requests\DiskBrowser\GetFilesRequest;
use App\Http\Requests\DiskBrowser\UploadFileRequest;

class FileController extends Controller
{
    /**
     * Return the list of files
     * @param $request
     * @return array
     */
    public function index(GetFilesRequest $request)
    {
        $browser = new DiskBrowser($request->input('disk'));

        return $browser->listFilesIn($request->input('path'));
    }

    /**
     * File upload
     * @param UploadFileRequest $request
     * @return array
     * @throws \Exception
     */
    public function store(UploadFileRequest $request)
    {
        $browser = new DiskBrowser($request->input('disk'));
        return $browser->createFile($request->file('file'), $request->input('path'));

    }

}
