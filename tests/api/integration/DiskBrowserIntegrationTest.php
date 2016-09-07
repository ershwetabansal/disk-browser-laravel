<?php

use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class DiskBrowserIntegrationTest extends TestCase
{

    use DatabaseTransactions;

    /**
     * @var string
     */
    private $testDirectory = 'elephants';

    /**
     * @var \App\DiskBrowser\DiskBrowser
     */
    private $browser;

    public function setUp()
    {
        parent::setUp();

        //Setup following directory structure
        /*
        | .
        |  /cats
        |       /cute
        |           cute_cat.png
        |       fat_cat.png
        |  /dogs
        |       /puppies
        |           /trained
        |               cute_and_trained_puppies.jpg
        |               trained_puppies.jpg
        |           cute_puppies.jpg
        |  /monkeys
        |       /angry
        |           angry_monkey.png
        |       /cute
        |           cute_monkey.png
        |  my-dog.jpg
        |  my-cat.jpg
        |  i-love-this-dog.jpg
        |  spreadsheet.xlsx
        */

        $this->browser = new \App\DiskBrowser\DiskBrowser('integration_tests');
    }

    public function tearDown()
    {
        $this->deleteDirectory($this->testDirectory);
        parent::tearDown();
    }

    /** @test */
    public function it_returns_list_of_directories_in_root_directory()
    {
        // When we setup the directory structure

        // And setup disk browser for 'integration_tests' disk

        // And get the list of directories in root directory
        $result = $this->browser->listDirectoriesIn('/');

        // Then we see three directories
        $this->assertEquals(sizeof($result), 3);

        $expectations = [
            [
                'name' => 'cats',
                'path' => '/',
            ],
            [
                'name' => 'dogs',
                'path' => '/',
            ],
            [
                'name' => 'monkeys',
                'path' => '/',
            ],
        ];

        // Which have name and path parameters
        for($i = 0; $i < sizeof($result); $i++) {
            $this->assertEquals($expectations[$i]['name'], $result[$i]['name']);
            $this->assertEquals($expectations[$i]['path'], $result[$i]['path']);
        }
    }



    /** @test */
    public function it_returns_list_of_directories_in_a_given_directory()
    {
        // When we setup the directory structure

        // And setup disk browser for 'integration_tests' disk

        // And get the list of directories in a given directory
        $result = $this->browser->listDirectoriesIn('/dogs');

        // Then we see one directory
        $this->assertEquals(sizeof($result), 1);

        $expectations = [
            [
                'name' => 'puppies',
                'path' => '/dogs/',
            ],
        ];

        // Which have name and path parameters
        for($i = 0; $i < sizeof($result); $i++) {
            $this->assertEquals($expectations[$i]['name'], $result[$i]['name']);
            $this->assertEquals($expectations[$i]['path'], $result[$i]['path']);
        }
    }

    /** @test */
    public function it_returns_list_of_directories_in_a_given_directory_inside_another_directory()
    {
        // When we setup the directory structure

        // And setup disk browser for 'integration_tests' disk

        // And get the list of directories in a given directory which is inside another directory
        $result = $this->browser->listDirectoriesIn('/dogs/puppies');

        // Then we see one directory
        $this->assertEquals(sizeof($result), 1);

        $expectations = [
            [
                'name' => 'trained',
                'path' => '/dogs/puppies/',
            ],
        ];

        // Which have name and path parameters
        for($i = 0; $i < sizeof($result); $i++) {
            $this->assertEquals($expectations[$i]['name'], $result[$i]['name']);
            $this->assertEquals($expectations[$i]['path'], $result[$i]['path']);
        }
    }

    /**
     * @test
     * @expectedException \App\Exceptions\Filesystem\PathNotFoundInDiskException
     */
    public function it_throws_an_exception_when_trying_to_get_directories_from_a_path_that_does_not_exist()
    {
        $this->browser->listDirectoriesIn('/this-does-not-exist');
    }

    /** @test */
    public function it_returns_list_of_files_in_a_root_directory()
    {
        // When we setup the directory structure

        // And setup disk browser for 'integration_tests' disk

        // And get the list of files in root path
        $result = $this->browser->listFilesIn('/');

        // Then we see three files in the root path
        $this->assertEquals(sizeof($result), 3);

        $expectations = [
            [
                'name' => 'i-love-this-dog.jpg',
                'path' => '/test/',
            ],
            [
                'name' => 'my-cat.jpg',
                'path' => '/test/'
            ],
            [
                'name' => 'my-dog.jpg',
                'path' => '/test/'
            ],
        ];

        //Which have name, path, size and modified_at parameters
        for($i = 0; $i < sizeof($result); $i++) {
            $this->assertEquals($expectations[$i]['name'], $result[$i]['name']);
            $this->assertEquals($expectations[$i]['path'], $result[$i]['path']);
            $this->assertTrue($result[$i]['size'] >= 0);
            $this->assertTrue($result[$i]['modified_at'] < date('Y-m-d H:i:s'));
        }

    }

    /** @test */
    public function it_returns_list_of_files_in_a_given_directory()
    {

        // When we setup the directory structure

        // And setup disk browser for 'integration_tests' disk

        // And get the list of files in a given directory
        $result = $this->browser->listFilesIn('/cats');

        // Then we see one file
        $this->assertEquals(sizeof($result), 1);

        $expectations = [
            [
                'name' => 'fat_cat.png',
                'path' => '/test/cats/',
            ],
        ];

        // Which have name, path, size and modified_at parameters
        for($i = 0; $i < sizeof($result); $i++) {
            $this->assertEquals($expectations[$i]['name'], $result[$i]['name']);
            $this->assertEquals($expectations[$i]['path'], $result[$i]['path']);
            $this->assertTrue($result[$i]['size'] >= 0);
            $this->assertTrue($result[$i]['modified_at'] < date('Y-m-d H:i:s'));
        }
    }

    /** @test */
    public function it_returns_list_of_files_in_a_given_directory_inside_another_directory()
    {
        // When we setup the directory structure

        // And setup disk browser for 'integration_tests' disk

        // And get the list of files in a given directory which is inside another directory
        $result = $this->browser->listFilesIn('/dogs/puppies');

        // Then we see one file
        $this->assertEquals(sizeof($result), 1);

        $expectations = [
            [
                'name' => 'cute_puppies.jpg',
                'path' => '/test/dogs/puppies/',
            ],
        ];

        // Which have name, path, size and modified_at parameters
        for($i = 0; $i < sizeof($result); $i++) {
            $this->assertEquals($expectations[$i]['name'], $result[$i]['name']);
            $this->assertEquals($expectations[$i]['path'], $result[$i]['path']);
            $this->assertTrue($result[$i]['size'] >= 0);
            $this->assertTrue($result[$i]['modified_at'] < date('Y-m-d H:i:s'));
        }
    }

    /**
     * @test
     * @expectedException \App\Exceptions\Filesystem\PathNotFoundInDiskException
     */
    public function it_throws_an_exception_when_trying_to_get_files_from_a_path_that_does_not_exist()
    {
        $this->browser->listFilesIn('/this-does-not-exist');
    }

    /** @test */
    public function it_creates_directory_in_root_directory()
    {
        // When we setup the directory structure

        // And setup disk browser for 'integration_tests' disk

        // And create a directory in root directory
        $result = $this->browser->createDirectory($this->testDirectory, '/');

        // Then we see newly created directories' name and path
        $expectations = [
            'name' => $this->testDirectory,
            'path' => '/',
        ];

        $this->assertEquals($expectations['name'], $result['name']);
        $this->assertEquals($expectations['path'], $result['path']);
    }

    /** @test */
    public function it_creates_directory_in_a_given_directory()
    {
        // When we setup the directory structure

        // And setup disk browser for 'integration_tests' disk

        // And create a directory in root directory
        $result = $this->browser->createDirectory($this->testDirectory, '/');

        // Then we see newly created directories' name and path
        $expectations = [
            'name' => $this->testDirectory,
            'path' => '/',
        ];

        $this->assertEquals($expectations['name'], $result['name']);
        $this->assertEquals($expectations['path'], $result['path']);
    }

    /**
     * @test
     * @expectedException \App\Exceptions\Filesystem\PathNotFoundInDiskException
     */
    public function it_throws_an_exception_when_trying_to_create_directory_in_a_path_that_does_not_exist()
    {
        // When we setup the directory structure

        // And setup disk browser for 'integration_tests' disk

        // And create a directory in a path that does not exist
        $this->browser->createDirectory($this->testDirectory, '/this-does-not-exist');
    }

    /**
     * @test
     * @expectedException \App\Exceptions\Filesystem\DirectoryAlreadyExistsException
     */
    public function it_does_not_create_a_directory_if_directory_already_exists_with_same_name()
    {
        // When we setup the directory structure

        // And setup disk browser for 'integration_tests' disk

        // And create a directory in root directory
        $this->browser->createDirectory($this->testDirectory, '/');

        //And then try to create the directory with the same name again
        $this->browser->createDirectory($this->testDirectory, '/');
    }

    /** @test */
    public function it_stores_file_in_a_given_directory()
    {
        // When we setup the directory structure

        // And setup disk browser for 'integration_tests' disk

        // And we create a directory in root directory
        $this->browser->createDirectory($this->testDirectory, '/');

        // And have a local file ready to upload
        $localFile = env('BASE_PATH') . 'tests/api/stubs/files/spreadsheet.xlsx';

        $uploadedFile = new Symfony\Component\HttpFoundation\File\UploadedFile(
            $localFile,
            'spreadsheet.xlsx',
            'application/vnd.ms-excel',
            null,
            null,
            true
        );

        $result = $this->browser->createFile($uploadedFile, '/' . $this->testDirectory);

        $this->assertContains('spreadsheet', $result['name']);
        $this->assertEquals('/test/' . $this->testDirectory . '/', $result['path']);
        $this->asserttrue($result['size'] >= 0);
        $this->asserttrue($result['modified_at'] <= date('Y-m-d H:i:s'));
    }

    /** @test */
    public function it_searches_files_and_directories_in_a_given_disk()
    {

    	// Given there is a disk called 'integration_tests'

        // And the disk has the usual directory structure:

    	// When we search disk with word 'cute'
        $result = $this->browser->search('cute');

        $files = $result['files'];

        $directories = $result['directories'];

        // We see four files and 2 directories
        $this->assertEquals(sizeof($files), 4);
        $this->assertEquals(sizeof($directories), 2);

        $expectations = [
            'directories' => [
                [
                    'name' => 'cute',
                    'path' => '/cats/',
                ],
                [
                    'name' => 'cute',
                    'path' => '/monkeys/',
                ]
            ],
            'files' => [
                [
                    'name' => 'cute_cat.png',
                    'path' => '/test/cats/cute/'
                ],
                [
                    'name' => 'cute_puppies.jpg',
                    'path' => '/test/dogs/puppies/'
                ],
                [
                    'name' => 'cute_and_trained_puppies.jpg',
                    'path' => '/test/dogs/puppies/trained/'
                ],
                [
                    'name' => 'cute_monkey.png',
                    'path' => '/test/monkeys/cute/'
                ]
            ]
        ];
        // Files have name, path, size and modified_at parameters
        for($i = 0; $i < sizeof($files); $i++) {
            $this->assertEquals($expectations['files'][$i]['name'], $files[$i]['name']);
            $this->assertEquals($expectations['files'][$i]['path'], $files[$i]['path']);
            $this->assertTrue($files[$i]['size'] >= 0);
            $this->assertTrue($files[$i]['modified_at'] < date('Y-m-d H:i:s'));
        }

        // Directories have name and path parameters
        for($i = 0; $i < sizeof($directories); $i++) {
            $this->assertEquals($expectations['directories'][$i]['name'], $directories[$i]['name']);
            $this->assertEquals($expectations['directories'][$i]['path'], $directories[$i]['path']);
        }
    }

    /** @test */
    public function it_deletes_an_empty_directory()
    {
        // Given there is a disk named 'integration_tests'.

        // And it has the usual directory structure.

        // And we create an empty directory in root of the given disk
        $this->browser->createDirectory($this->testDirectory, '/');

        // we should be able to delete the test directory
        $this->assertTrue($this->browser->deleteDirectory($this->testDirectory, '/'));

        $this->assertFalse($this->doesExist('/' . $this->testDirectory));
    }

    /**
     * @test
     * @expectedException \App\Exceptions\Filesystem\DirectoryIsNotEmptyException
     */
    public function it_throws_an_exception_if_trying_to_delete_a_directory_that_has_subdirectories()
    {
        // Given there is a disk named 'integration_tests'.

        // And it has the usual directory structure.

        // And we try to delete a directory which has sub-directories
        $this->browser->deleteDirectory('cats', '/');

        $this->assertTrue($this->doesExist('/cats'));

    }

    /**
     * @test
     * @expectedException \App\Exceptions\Filesystem\DirectoryIsNotEmptyException
     */
    public function it_throws_an_exception_if_trying_to_delete_a_directory_that_has_files()
    {
        // Given there is a disk named 'integration_tests'.

        // And it has the usual directory structure.

        // And we try to delete a directory which has files in it
        $this->browser->deleteDirectory('cute', '/cats/');

        $this->assertTrue($this->doesExist('/cats/cute'));

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
