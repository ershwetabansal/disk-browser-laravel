<?php

use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class DiskBrowserIntegrationTest extends TestCase
{

    use WithoutMiddleware, DatabaseTransactions;

    private $testDirectory = 'elephants';

    private $browser;

    public function setUp()
    {
        parent::setUp();

        //Setup following directory structure
        /*
        | .
        |  /cats
        |       /cute
        |       fat_cat.png
        |  /monkeys
        |       /angry
        |       /cute
        |  /dogs
        |       /puppies
        |           /trained
        |               trained_puppies.jpg
        |               cute_and_trained_puppies.jpg
        |           cute_puppies.jpg
        |  my-dog.jpg
        |  my-cat.jpg
        |  i-love-this-dog.jpg
        */

        $this->browser = new \App\LocalBrowser('integration_tests');
    }

    public function tearDown()
    {
        parent::tearDown();

        
    }

    /** @test */
    public function it_returns_files_in_a_root_directory()
    {

        
    }

    /** @test */
    public function it_returns_files_in_a_given_directory()
    {

    }

    /** @test */
    public function it_returns_directories_in_root_directory()
    {

    }

    /** @test */
    public function it_returns_directories_in_a_given_directory()
    {

    }

    /** @test */
    public function it_creates_directory_in_root_directory()
    {

    }

    /** @test */
    public function it_creates_directory_in_a_given_directory()
    {

    }

    /** @test */
    public function it_does_not_create_a_directory_if_directory_already_exists_with_same_name()
    {

    }

    /** @test */
    public function it_stores_file_in_a_given_directory()
    {

    }

    /** @test */
    public function it_searches_files_and_directories_in_a_given_disk()
    {

    	// Given there is a disk called 'integration_tests'

        // And the disk has the usual directory structure:

    	// When we search disk with word 'cute'
    	$this->assertEquals($this->browser->searchDisk('cute'),
    		[
                    'directories' => [
                        [
                            'name' => 'cute',
                            'path' => '/cats/cute',
                        ],
                        [
                            'name' => 'cute',
                            'path' => '/monkeys/cute',
                        ]
                    ],
                    'files' => [
                        [
                            'name' => 'cute_cat.png',
                            'path' => '/cats/cute/cute_cat.png'
                        ],
                        [
                            'name' => 'cute_puppies.jpg',
                            'path' => '/dogs/puppies/cute_puppies.jpg'
                        ],
                        [
                            'name' => 'cute_and_trained_puppies.jpg',
                            'path' => '/dogs/puppies/trained/cute_and_trained_puppies.jpg'
                        ],
                        [
                            'name' => 'cute_monkey.png',
                            'path' => '/monkeys/cute/cute_monkey.png'
                        ]
                    ]
                ]
        );


    }

}
