<?php

use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class FileTest extends TestCase
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
//        $this->deleteDirectory($this->testDirectory);
        
    }

    public function it_uploads_a_file_in_a_given_directory()
    {

    }

    public function it_does_not_upload_a_file_if_directory_does_not_exist()
    {

    }

    public function it_generates_a_unique_name_for_a_file()
    {

    }

    public function it_returns_only_the_files_present_in_a_given_directory()
    {

    }

    public function it_returns_size_of_a_given_directory()
    {

    }

    public function it_returns_size_as_zero_if_the_requested_file_does_not_exist()
    {

    }

    public function it_returns_last_modified_date_of_requested_file()
    {

    }

    public function it_returns_all_files_recursively_in_a_given_path_of_a_given_disk()
    {

    }

    public function it_returns_file_meta_data_for_a_requested_file()
    {

    }

    public function it_does_not_return_file_meta_data_if_file_is_hidden()
    {

    }

    public function it_returns_array_of_all_files_matching_a_given_word_in_a_given_disk()
    {

    }

    public function it_returns_list_of_files_including_the_file_which_matches_searched_word_but_has_a_different_case()
    {

    }

    /** @test */
    public function it_returns_true_if_file_name_has_extension()
    {
        $this->assertTrue(\App\Filesystem\File::doesTheFileHasExtension('i-love-this-dog.jpg'));
    }

    /** @test */
    public function it_returns_false_if_file_name_does_not_have_extension()
    {
        $this->assertFalse(\App\Filesystem\File::doesTheFileHasExtension('i-love-this-dog'));

        $this->assertFalse(\App\Filesystem\File::doesTheFileHasExtension('i-love-this-dog.'));
    }

    /** @test */
    public function it_returns_true_if_file_extension_is_allowed_on_given_disk()
    {
        $this->assertTrue(\App\Filesystem\File::isFileAllowedOnDisk('/i-love-this-dog.jpg', 'integration_tests'));
    }

    /** @test */
    public function it_returns_false_if_file_extension_is_not_allowed_on_given_disk()
    {
        $this->assertFalse(\App\Filesystem\File::isFileAllowedOnDisk('/i-love-this-dog.xlsx', 'integration_tests'));

        $this->assertFalse(\App\Filesystem\File::isFileAllowedOnDisk('/i-love-this-dog.doc', 'integration_tests'));

        $this->assertFalse(\App\Filesystem\File::isFileAllowedOnDisk('/.DS_Store', 'integration_tests'));
    }

    /** @test */
    public function it_returns_true_if_file_is_a_hidden_file()
    {
        $this->assertTrue(\App\Filesystem\File::isGivenFileHidden('.DS_Store'));
    }

    /** @test */
    public function it_returns_false_if_file_is_not_a_hidden_file()
    {
        $this->assertFalse(\App\Filesystem\File::isGivenFileHidden('i-love-this-dog.png'));
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
