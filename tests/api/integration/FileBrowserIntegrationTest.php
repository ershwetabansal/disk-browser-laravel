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
        $directories = \App\LocalFileBrowser::directoriesStructure('/public/');

        //It should contain more than one directories
        $this->assertEquals(sizeof($directories),2);

        dd($directories);
        //And the sub directory should further have directories

    }
}
