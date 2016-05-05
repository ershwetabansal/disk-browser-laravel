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
        $this->deleteDirectory($this->testDirectory);
        
    }

    /** @test */
    public function it_returns_list_of_files_in_a_root_directory()
    {
        //When we setup the directory structure

        //And setup disk browser for 'integration_tests' disk

        //And get the list of files in root path
        $result = $this->browser->listFilesIn();

        //Then we see three files in the root path
        $this->assertEquals(sizeof($result), 3);
        //Names of the files
        $this->assertContains('i-love-this-dog.jpg', $result);
        $this->assertContains('my-cat.jpg', $result);
        $this->assertContains('my-dog.jpg', $result);

        $this->assertContains([
            'name' => 'i-love-this-dog.jpg',
            'path' => '/i-love-this-dog.jpg'
        ], $result);

        $this->assertContains([
            'name' => 'my-cat.jpg',
            'path' => '/my-cat.jpg'
        ], $result);

        $this->assertContains([
            'name' => 'my-dog.jpg',
            'path' => '/my-dog.jpg'
        ], $result);

    }

    /** @test */
    public function it_returns_list_of_files_in_a_given_directory()
    {
        $this->assertEquals($this->browser->listFilesIn('/dogs/puppies'), []);
    }

    /** @test */
    public function it_returns_list_of_files_in_a_given_directory_inside_another_directory()
    {
        $this->assertEquals($this->browser->listFilesIn('/dogs/puppies/trained'), []);
    }

    /** @test */
    public function it_returns_empty_array_when_requested_from_non_existing_path()
    {
        $this->assertEquals($this->browser->listFilesIn('/elephants'), []);
    }

    /** @test */
    public function it_returns_list_of_directories_in_root_directory()
    {
        $this->assertEquals($this->browser->listDirectoriesIn(), []);
    }

    /** @test */
    public function it_returns_list_of_directories_in_a_given_directory()
    {
        $this->assertEquals($this->browser->listDirectoriesIn('/dogs'), []);
    }

    /** @test */
    public function it_returns_list_of_directories_in_a_given_directory_inside_another_directory()
    {
        $this->assertEquals($this->browser->listDirectoriesIn('/dogs/puppies'), []);
    }

    /** @test */
    public function it_creates_directory_in_root_directory()
    {
        $this->assertEquals($this->browser->createDirectory($this->testDirectory), []);
    }

    /** @test */
    public function it_creates_directory_in_a_given_directory()
    {
        $this->assertEquals($this->browser->createDirectory($this->testDirectory), []);

        $this->assertEquals($this->browser->createDirectory($this->testDirectory, '/' . $this->testDirectory), []);
    }

    /** @test */
    public function it_does_not_create_a_directory_if_directory_already_exists_with_same_name()
    {
        $this->assertEquals($this->browser->createDirectory($this->testDirectory), []);

        $this->assertEquals($this->browser->createDirectory($this->testDirectory), []);
    }

    /** @test */
    public function it_stores_file_in_a_given_directory()
    {
        $localFile = env('BASE_PATH') . 'tests/stubs/files/spreadsheet.xlsx';

        $uploadedFile = new Symfony\Component\HttpFoundation\File\UploadedFile(
            $localFile,
            'spreadsheet.xlsx',
            'application/vnd.ms-excel',
            null,
            null,
            true
        );

        $this->assertEquals($this->browser->createFile($uploadedFile, $this->testDirectory), []);
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


    /**
     * Delete a given directory
     * @param string $directory
     */
    private function deleteDirectory($directory)
    {
        if ($this->doesExist($directory)) {
            Storage::disk('integration_tests')->deleteDirectory($directory);
        }
    }

    /**
     * Returns true if path exists in the given directory
     * @param string $path
     * @return boolean
     */
    private function doesExist($path)
    {
        return Storage::disk('integration_tests')->has($path);
    }


}
