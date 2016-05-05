<?php

use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class DirectoryTest extends TestCase
{

    use DatabaseTransactions;

    private $testDirectory = 'elephants';
    private $diskName = 'integration_disks';

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
