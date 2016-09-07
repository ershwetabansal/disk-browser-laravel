<?php

use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class PathTest extends TestCase
{

    use DatabaseTransactions;


    /** @test */
    public function it_returns_the_directory_name_from_a_given_directory_path()
    {
        $this->assertEquals(\App\DiskBrowser\Path::stripName('/cats'), 'cats');

        $this->assertEquals(\App\DiskBrowser\Path::stripName('/cats/dogs'), 'dogs');

        $this->assertEquals(\App\DiskBrowser\Path::stripName('/cats/dogs/trained'), 'trained');
    }

    /** @test */
    public function it_returns_file_name_from_a_given_file_path()
    {
        $this->assertEquals(\App\DiskBrowser\Path::stripName('/cats/cat.png'), 'cat.png');

        $this->assertEquals(\App\DiskBrowser\Path::stripName('/cats/dogs/dog.png'), 'dog.png');

        $this->assertEquals(\App\DiskBrowser\Path::stripName('/cats/dogs/trained/trained.png'), 'trained.png');
    }

    /** @test */
    public function it_returns_valid_path_from_a_given_string_when_there_is_no_trailing_separator()
    {

        $this->assertEquals(\App\DiskBrowser\Path::normalize('cats/cat'), '/cats/cat/');
    }

    /** @test */
    public function it_returns_valid_path_from_a_given_string_when_there_is_a_leading_separator()
    {
        $this->assertEquals(\App\DiskBrowser\Path::normalize('/cats/cat/'), '/cats/cat/');
    }

    /** @test */
    public function it_does_not_change_a_path_when_in_correct_format()
    {
        $this->assertEquals(\App\DiskBrowser\Path::normalize('cats/cat/'), '/cats/cat/');
    }

    /** @test */
    public function it_does_not_change_a_root_directory_path()
    {
        $this->assertEquals(\App\DiskBrowser\Path::normalize('/'), '/');
    }

    /** @test */
    public function it_returns_directory_from_a_file_path()
    {
        $this->assertEquals(\App\DiskBrowser\Path::getDirectoryFromFullPath('/cats/cute/cat.png'), '/cats/cute');
    }

    /** @test */
    public function it_returns_directory_from_a_directory_path()
    {
        $this->assertEquals(\App\DiskBrowser\Path::getDirectoryFromFullPath('/cats/cute_angry/cute'), '/cats/cute_angry');
    }

}
