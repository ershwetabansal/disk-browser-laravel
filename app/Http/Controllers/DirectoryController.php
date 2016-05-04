<?php

namespace App\Http\Controllers;


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
