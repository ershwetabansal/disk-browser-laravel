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

            var_dump($attribute);
            var_dump($value);
            var_dump($parameters);
            var_dump($validator->getData()['disk']);
            var_dump(Directory::doesDirectoryExist($value, $validator->getData()['disk'], $validator->getData()['path']));
//            return true;
            return (Directory::doesDirectoryExist($value, $validator->getData()['disk'], $validator->getData()['path']) == false);

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
