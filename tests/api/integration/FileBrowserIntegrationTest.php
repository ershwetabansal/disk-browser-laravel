<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class FileBrowserIntegrationTest extends TestCase
{
    /** @test */
    public function it_should_return_directories_hierarchy()
    {
        //Get the directories structure for public directory
        $directories = \App\LocalFileBrowser::directoriesStructure('/public/assets');

        //It should contain more than one directories
        $this->assertGreaterThan(0, sizeof($directories));

        //And the sub directory should further have directories
        foreach($directories as $directory) {
            $this->assertGreaterThanOrEqual(0, sizeof($directory['directories']));
        }
    }

    /** @test */
    public function it_should_return_files_in_directory()
    {
        //Get the files from a particular directory
        $files = \App\LocalFileBrowser::filesInDirectory('/public/assets/cats');

        //It should contain more than one files
        $this->assertGreaterThan(0, sizeof($files));

    }

    /** @test */
    public function it_should_get_file_size()
    {
        //Get the file size
        $size = \App\LocalFileBrowser::size('/public/assets/Asaam.jpg');

        //File size should be greater than 0
        $this->assertGreaterThan(0, $size);

    }

    /** @test */
    public function it_should_get_file_last_modified_date()
    {
        //Get the file last modified date which should exist
        $date = \App\LocalFileBrowser::lastModified('/public/assets/Asaam.jpg');

        $this->assertEquals('13 Apr 2016 07:36:34', $date);
    }

    /** @test */
    public function it_should_return_files_with_meta_data()
    {
        //Get all the files in a directory
        $files = \App\LocalFileBrowser::files('/public/assets');

        //It should contain more than one files
        $this->assertGreaterThan(0, sizeof($files));

        //Each file should have 'name', 'path', 'size' and 'last_modified_date'
        foreach($files as $file) {

        }
    }
}
