<?php

namespace App\Exceptions;

use App\Exceptions\Filesystem\DirectoryAlreadyExistsException;
use App\Exceptions\Filesystem\PathNotFoundInDiskException;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that should not be reported.
     *
     * @var array
     */
    protected $dontReport = [
        HttpException::class,
        ModelNotFoundException::class,
    ];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param  \Exception  $e
     * @return void
     */
    public function report(Exception $e)
    {
        return parent::report($e);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $e
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $e)
    {
        if ($e instanceof PathNotFoundInDiskException) {
            return response(['errors' => ['Path does not exist.']],422);
        }

        if ($e instanceof DirectoryAlreadyExistsException) {
            return response(['errors' => ['Directory already exists in the given path.']],422);
        }

        return parent::render($request, $e);
    }
}
