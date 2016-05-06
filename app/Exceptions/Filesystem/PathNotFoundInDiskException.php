<?php

namespace App\Exceptions\Filesystem;

use Exception;

class PathNotFoundInDiskException extends Exception
{
    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $e
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $e)
    {
        return response([
            'success' => false,
            'errors' => ['Path does not exist.']
        ], 422);
    }

}