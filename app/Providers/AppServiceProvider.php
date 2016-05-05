<?php

namespace App\Providers;

use App\Filesystem\Directory;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        \Validator::extend('directory_not_exists', function($attribute, $value, $parameters, $validator) {

            $path = (isset($validator->getData()['path'])) ? $validator->getData()['path'] : DIRECTORY_SEPARATOR;
            return (Directory::doesDirectoryExist($value, $validator->getData()['disk'], $path) == false);

        });

        \Validator::extend('path_exists', function($attribute, $value, $parameters, $validator) {

            if (isset($value)) {
                return (Directory::doesPathExist($validator->getData()['disk'], $value));
            }

            return true;

        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {

    }
}
