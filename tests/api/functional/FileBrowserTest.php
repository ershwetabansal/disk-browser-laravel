<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class FileBrowserTest extends TestCase
{
    /** @test */
    public function it_should_return_directories_hierarchy()
    {
        $this->post('/api/v1/directories', ['root' => 'images'])
            ->seeJson([

            ]);
    }
}
