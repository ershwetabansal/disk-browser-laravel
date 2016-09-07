<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests;
use Illuminate\Http\Request;
use App\DiskBrowser\DiskBrowser;
use App\Http\Controllers\Controller;
use App\Http\Requests\DiskBrowser\DiskSearchRequest;

class DiskController extends Controller
{

    /**
     * Return the list of directories and files in a disk where name contains a given string
     * @param $request
     * @return array
     */
    public function search(DiskSearchRequest $request)
    {
        $browser = new DiskBrowser($request->input('disk'));

        return $browser->search($request->input('search'));
    }

}
