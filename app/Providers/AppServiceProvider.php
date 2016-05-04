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
        \Validator::extend('directoryNotExists', function($attribute, $value, $parameters, $validator) {

            $path = (isset($validator->getData()['path'])) ? $validator->getData()['path'] : '/';
            return (Directory::doesDirectoryExist($value, $validator->getData()['disk'], $path) == false);

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
