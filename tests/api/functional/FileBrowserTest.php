<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class FileBrowserTest extends TestCase
{
    /** @test */
    public function it_should_return_directories_hierarchy()
    {
        //When I hit the directories API, it should return JSON with list of directories
        $this->post('/api/v1/directories', ['disk' => 'assets'])
            ->seeJson([
                'directories' => []
            ]);
    }


    /** @test */
    public function it_should_return_files_in_a_directory()
    {
        //When I hit the files API, it should return JSON with all the files in that path
        $this->post('/api/v1/files', ['disk' => 'assets', 'path' => 'public/assets/cats'])
            ->seeJson([
                'directories' => []
            ]);
    }

}
