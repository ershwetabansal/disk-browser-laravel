<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Http\Requests\GetFilesRequest;

class FileController extends Controller
{
    /**
     * Return the list of files
     * @param $request
     * @return array
     */
    public function index(GetFilesRequest $request)
    {
        $path = \App\LocalFileBrowser::getFilePath($request->all()['disk'], $request->all()['path']);
        $files = [];

        if ($path) {
            $files = \App\LocalFileBrowser::files($path);
        }

        return $files;
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     *
     * @param Request $request
     * @return array
     * @throws \Exception
     */
    public function store(Request $request)
    {
        $upload = $request->file('file');

        if (! $upload->isValid()) {
            throw new \Exception('Invalid file uploaded.');
        }

        $content = file_get_contents($upload->getRealPath());

        $newFileName = time() . '_' . uniqid() . '.' . $upload->getClientOriginalExtension();

        $path = \App\LocalFileBrowser::getFilePath($request->all()['disk'], $request->all()['path']);

        \App\LocalFileBrowser::uploadFile($path, $newFileName, $content);

        return [
            'path' => $path . $newFileName,
            'name' => $newFileName,
            'size' => \App\LocalFileBrowser::size($path . "/" . $newFileName),
            'last_modified_date' => \App\LocalFileBrowser::lastModified($path . "/" . $newFileName),
        ];

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
