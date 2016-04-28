<?php

use Mockery as m;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class DirectoryUnitTest extends TestCase
{
    private $filesystem;

    private $directory;

    public function setUp()
    {
        parent::setUp();

        $this->filesystem = m::mock('Illuminate\Filesystem\Filesystem');

        $this->directory = new \App\Filesystem\Directory(
            $this->filesystem, '/');
    }

    /** @test */
    public function test_it_can_be_instantiated()
    {
        $this->assertInstanceOf('App\Filesystem\Directory', $this->directory);
    }

    /** @test */
    public function it_can_return_its_path()
    {
        $this->assertEquals('/', $this->directory->path());
    }

    /** @test */
    public function it_returns_a_list_of_all_files_within_the_directory()
    {
        $files = [
            '2016-02-01_Perspectives.pdf',
            '2016-01_15_In_focus.pdf',
            '2016-01_04_Data_review.pdf',
        ];

        $mock = m::mock('Illuminate\Filesystem\Filesystem');
        $mock->shouldReceive('files')->once()->andReturn($files);

        $directory = new \App\Filesystem\Directory($mock, '/');

        $this->assertEquals($files, $directory->files());
    }

    /** @test */
    public function it_returns_a_list_of_all_sub_directories_it_holds()
    {
        $subDirectories = [
            'publications',
            'images',
        ];

        $mock = m::mock('Illuminate\Filesystem\Filesystem');
        $mock->shouldReceive('directories')->once()->andReturn($subDirectories);

        $directory = new \App\Filesystem\Directory($mock, '/');

        $this->assertEquals($subDirectories, $directory->directories());
    }

    /** @test */
    public function it_throws_an_exception_if_a_path_has_not_been_set()
    {
        $this->setExpectedException('App\Exceptions\Filesystem\MissingPathException');

        $mock = m::mock('Illuminate\Filesystem\Filesystem');

        $directory = new \App\Filesystem\Directory($mock);

        $directory->files();
    }
}
