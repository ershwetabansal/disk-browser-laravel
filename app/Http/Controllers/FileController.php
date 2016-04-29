<?php

namespace App\Http\Controllers;

use App\LocalBrowser;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Http\Requests\GetFilesRequest;
use App\Http\Requests\UploadFileRequest;

class FileController extends Controller
{
    /**
     * Return the list of files
     * @param $request
     * @return array
     */
    public function index(GetFilesRequest $request)
    {
        $browser = new LocalBrowser($request->input('disk'));

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
        $browser = new LocalBrowser($request->input('disk'));

        return $browser->createFileIn($request->input('path'), $request->file('file'));

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
