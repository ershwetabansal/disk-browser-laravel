<?php

use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class DirectoryTest extends TestCase
{

    use DatabaseTransactions;

    private $testDirectory = 'elephants';
    private $testDisk = 'integration_tests';

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

    }

    public function tearDown()
    {
        $this->deleteDirectory($this->testDirectory);
        parent::tearDown();

    }

    /** @test */
    public function it_creates_a_directory_in_root_directory()
    {

        // Given there is a disk called 'integration_tests'.

        // And it has the usual directory structure.

        // And we create a test directory inside the root directory
        $directory = \App\DiskBrowser\Directory::createDirectory($this->testDirectory, $this->testDisk, '/');

        $this->assertEquals($directory, true);

    }

    /** @test */
    public function it_creates_a_directory_in_a_given_directory()
    {
        // Given there is a disk called 'integration_tests'.

        // And it has the usual directory structure.

        // And there is one test directory inside the root
        \App\DiskBrowser\Directory::createDirectory($this->testDirectory, $this->testDisk, '/');

        // Now we create another directory inside test directory
        $directory = \App\DiskBrowser\Directory::createDirectory($this->testDirectory, $this->testDisk, '/' . $this->testDirectory);

        $this->assertEquals($directory, true);

    }

    /** @test */
    public function it_returns_true_if_directory_exists_in_root_directory()
    {

        // Given there is a disk called 'integration_tests'.

        // And it has the usual directory structure.

        // It returns true if we check whether 'cats' directory exists in root
        $this->assertEquals(true, \App\DiskBrowser\Directory::exists($this->testDisk, "/cats"));

    }

    /** @test */
    public function it_returns_true_if_given_directory_does_not_exist_in_root()
    {
        // Given there is a disk called 'integration_tests'.

        // And it has the usual directory structure.

        // It returns true if we check whether 'horse' directory exists in root
        $this->assertEquals(true, \App\DiskBrowser\Directory::notExists('horse', $this->testDisk, '/'));

    }

    /** @test */
    public function it_returns_true_when_directory_does_not_exists_in_a_given_path()
    {
        // Given there is a disk called 'integration_tests'.

        // And it has the usual directory structure.

        $this->assertTrue(\App\DiskBrowser\Directory::notExists('this-does-not-exist', $this->testDisk, '/dogs/'));
    }

    /** @test */
    public function it_returns_only_directories_in_a_given_path()
    {
        // Given there is a disk called 'integration_tests'.

        // And it has the usual directory structure.

        // When we get directories in the root
        $directories = \App\DiskBrowser\Directory::directoriesIn($this->testDisk, '/');

        // We see total 3 directories
        $this->assertCount(3, $directories);

        $this->assertEquals($directories, ['cats', 'dogs', 'monkeys']);
    }

    /** @test */
    public function it_returns_all_directories_recursively_in_a_given_path()
    {
        // Given there is a disk called 'integration_tests'.

        // And it has the usual directory structure.

        // When we get all directories in the root
        $directories = \App\DiskBrowser\Directory::allDirectoriesIn($this->testDisk, '/');

        // We see total 7 directories
        $this->assertCount(8, $directories);

        $this->assertEquals($directories,
            ['cats', 'cats/cute', 'dogs', 'dogs/puppies', 'dogs/puppies/trained', 'monkeys', 'monkeys/angry', 'monkeys/cute']);

    }

    /** @test */
    public function it_returns_directory_name_and_path()
    {
        // Given there is a disk called 'integration_tests'.

        // And it has the usual directory structure.

        // It should return directory meta data
        $this->assertEquals(\App\DiskBrowser\Directory::metaDataOf('/cats', $this->testDisk),
            ['name' => 'cats',
             'path' => '/']
        );

    }

    /** @test */
    public function it_returns_list_of_directories_matching_a_particular_word_in_a_given_disk()
    {
        // Given there is a disk called 'integration_tests'.

        // And it has the usual directory structure.

        // When we search the disk for all directories containing 'cute' word
        $result = \App\DiskBrowser\Directory::searchDisk('cute', $this->testDisk);

        $expectations = [
            [
                'name' => 'cute',
                'path' => '/cats/',
            ],
            [
                'name' => 'cute',
                'path' => '/monkeys/',
            ]
        ];

        // We see two results
        $this->assertCount(2, $result);

        for($i = 0; $i < sizeof($result); $i++) {
            $this->assertEquals($expectations[$i]['name'], $result[$i]['name']);
            $this->assertEquals($expectations[$i]['path'], $result[$i]['path']);
        }
    }

    /** @test */
    public function it_returns_empty_array_if_there_is_no_directory_matching_a_particular_word_in_a_given_disk()
    {
        // Given there is a disk called 'integration_tests'.

        // And it has the usual directory structure.

        // When we search the disk for all directories containing 'not-available' word
        $result = \App\DiskBrowser\Directory::searchDisk('not-available', $this->testDisk);

        // We see no results
        $this->assertCount(0, $result);

        $this->assertEquals([], $result);
    }

    /** @test */
    public function it_returns_list_of_directories_including_the_directory_which_matches_searched_word_but_has_a_different_case()
    {
        // Given there is a disk called 'integration_tests'.

        // And it has the usual directory structure.

        // When we search the disk for all directories containing 'PUPP' word
        $result = \App\DiskBrowser\Directory::searchDisk('PUPP', $this->testDisk);

        $expectations = [
            [
                'name' => 'puppies',
                'path' => '/dogs/',
            ]
        ];

        // We see two results
        $this->assertCount(1, $result);

        for($i = 0; $i < sizeof($result); $i++) {
            $this->assertEquals($expectations[$i]['name'], $result[$i]['name']);
            $this->assertEquals($expectations[$i]['path'], $result[$i]['path']);
        }
    }

    /**
     * @test
     * @expectedException \App\Exceptions\Filesystem\PathNotFoundInDiskException
     */
    public function it_throws_an_exception_when_path_does_not_exist()
    {
        // Given there is a disk called 'integration_tests'.

        // And it has the usual directory structure.

        $this->assertFalse(\App\DiskBrowser\Directory::exists($this->testDisk, '/this-path-does-not-exist'));
    }

    /** @test */
    public function it_returns_true_when_path_exists()
    {
        // Given there is a disk called 'integration_tests'.

        // And it has the usual directory structure.

        $this->assertTrue(\App\DiskBrowser\Directory::exists($this->testDisk, '/cats'));
    }

    /**
     * @test
     * @expectedException \App\Exceptions\Filesystem\DirectoryAlreadyExistsException
     */
    public function it_throws_an_exception_when_directory_already_exists_in_root_of_a_given_disk()
    {
        // Given there is a disk called 'integration_tests'.

        // And it has the usual directory structure.

        $this->assertFalse(\App\DiskBrowser\Directory::notExists('cats', $this->testDisk, '/'));
    }

    /**
     * @test
     * @expectedException \App\Exceptions\Filesystem\DirectoryAlreadyExistsException
     */
    public function it_throws_an_exception_when_directory_already_exists_in_a_given_path()
    {
        // Given there is a disk called 'integration_tests'.

        // And it has the usual directory structure.

        $this->assertFalse(\App\DiskBrowser\Directory::notExists('trained', $this->testDisk, '/dogs/puppies/'));
    }

    /** @test */
    public function it_returns_true_if_a_directory_is_empty()
    {
        // Given there is a disk named 'integration_tests'.

        // And it has the usual directory structure.

        // And there is an empty directory in the root of the disk
        \App\DiskBrowser\Directory::createDirectory($this->testDirectory, $this->testDisk, '/');

        // Then we see that the directory is empty
        $this->assertTrue(\App\DiskBrowser\Directory::isEmpty($this->testDirectory, $this->testDisk));
    }

    /** @test */
    public function it_returns_false_if_a_directory_is_not_empty()
    {
        // Given there is a disk named 'integration_tests'.

        // And it has the usual directory structure.

        // And there is non empty directory in the disk

        // If directory has subdirectories, we see that directory is not empty
        $this->assertFalse(\App\DiskBrowser\Directory::isEmpty('cats', $this->testDisk));

        // If directory has files, we see that directory is not empty
        $this->assertFalse(\App\DiskBrowser\Directory::isEmpty('cats/cute', $this->testDisk));

    }

    /** @test */
    public function it_deletes_an_empty_directory()
    {
        // Given there is a disk named 'integration_tests'.

        // And it has the usual directory structure.

        // When we add a directory in the root of the disk
        \App\DiskBrowser\Directory::createDirectory($this->testDirectory, $this->testDisk, '/');

        // We are able to delete the same
        $this->assertTrue(\App\DiskBrowser\Directory::delete($this->testDirectory, $this->testDisk));

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

        // We can not delete the directory which has subdirectories
        \App\DiskBrowser\Directory::delete('cats', $this->testDisk);

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

        // We can not delete the directory which has files
        \App\DiskBrowser\Directory::delete('cats/cute', $this->testDisk);

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
