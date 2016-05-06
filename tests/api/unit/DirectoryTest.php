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
        parent::tearDown();
        $this->deleteDirectory($this->testDirectory);
        
    }

    public function it_creates_a_directory_in_root_directory()
    {
        $directory = \App\Filesystem\Directory::createDirectory($this->testDirectory, $this->testDisk);
    }

    public function it_creates_a_directory_in_a_given_directory()
    {

    }

    public function it_returns_true_if_directory_exists_in_root_directory()
    {

    }

    public function it_returns_false_if_directory_does_not_exist_in_root_directory()
    {

    }

    public function it_returns_true_if_directory_exists_in_given_path()
    {

    }

    public function it_returns_false_if_directory_does_not_exist_in_given_path()
    {

    }

    public function it_returns_true_if_path_exists()
    {

    }

    public function it_returns_false_if_path_does_not_exist()
    {

    }

    public function it_returns_only_directories_in_a_given_path()
    {

    }

    public function it_returns_all_directories_recursively_in_a_given_path()
    {

    }

    public function it_returns_directory_name_and_path()
    {

    }

    public function it_returns_list_of_directories_matching_a_particular_word_in_a_given_disk()
    {

    }

    public function it_returns_empty_array_if_there_is_no_directory_matching_a_particular_word_in_a_given_disk()
    {

    }

    public function it_returns_list_of_directories_including_the_directory_which_matches_searched_word_but_has_a_different_case()
    {

    }

    /**
     * @test
     * @expectedException \App\Exceptions\Filesystem\PathNotFoundInDiskException
     */
    public function it_throws_an_exception_when_path_does_not_exist()
    {
        $this->assertFalse(\App\Filesystem\Directory::exists($this->testDisk, '/this-path-does-not-exist'));
    }

    /** @test */
    public function it_returns_true_when_path_exists()
    {
        $this->assertTrue(\App\Filesystem\Directory::exists($this->testDisk, '/cats'));
    }

    /**
     * @test
     * @expectedException \App\Exceptions\Filesystem\DirectoryAlreadyExistsException
     */
    public function it_throws_an_exception_when_directory_already_exists_in_root_of_a_given_disk()
    {
        $this->assertFalse(\App\Filesystem\Directory::notExists('cats', $this->testDisk));
    }

    /**
     * @test
     * @expectedException \App\Exceptions\Filesystem\DirectoryAlreadyExistsException
     */
    public function it_throws_an_exception_when_directory_already_exists_in_a_given_path()
    {
        $this->assertFalse(\App\Filesystem\Directory::notExists('trained', $this->testDisk, '/dogs/puppies/'));
    }

    /** @test */
    public function it_returns_true_when_directory_does_not_exists_in_a_given_path()
    {
        $this->assertTrue(\App\Filesystem\Directory::notExists('this-does-not-exist', $this->testDisk, '/dogs/'));
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
