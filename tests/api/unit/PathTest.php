<?php

use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class PathTest extends TestCase
{

    use DatabaseTransactions;


    /** @test */
    public function it_should_return_the_directory_name_from_a_given_directory_path()
    {
        $this->assertEquals(\App\Filesystem\Path::stripName('/cats'), 'cats');

        $this->assertEquals(\App\Filesystem\Path::stripName('/cats/dogs'), 'dogs');

        $this->assertEquals(\App\Filesystem\Path::stripName('/cats/dogs/trained'), 'trained');

    }

    /** @test */
    public function it_should_return_file_name_from_a_given_file_path()
    {
        $this->assertEquals(\App\Filesystem\Path::stripName('/cats/cat.png'), 'cat.png');

        $this->assertEquals(\App\Filesystem\Path::stripName('/cats/dogs/dog.png'), 'dog.png');

        $this->assertEquals(\App\Filesystem\Path::stripName('/cats/dogs/trained/trained.png'), 'trained.png');

    }

    /** @test */
    public function it_should_return_valid_path_from_a_given_string_when_there_is_no_trailing_separator()
    {

        $this->assertEquals(\App\Filesystem\Path::valid('cats/cat'), '/cats/cat/');
    }

    /** @test */
    public function it_should_return_valid_path_from_a_given_string_when_there_is_a_leading_separator()
    {
        $this->assertEquals(\App\Filesystem\Path::valid('/cats/cat/'), '/cats/cat/');
    }

    /** @test */
    public function it_should_not_change_a_path_when_in_correct_format()
    {
        $this->assertEquals(\App\Filesystem\Path::valid('cats/cat/'), '/cats/cat/');
    }

    /** @test */
    public function it_should_not_change_a_root_directory_path()
    {
        $this->assertEquals(\App\Filesystem\Path::valid('/'), '/');
    }

}
