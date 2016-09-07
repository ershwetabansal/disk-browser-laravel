<?php

use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class FileTest extends TestCase
{

    use DatabaseTransactions;

    private $testDirectory = 'elephants';
    private $testFile = 'elephant.jpg';
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
        $this->deleteFile($this->testFile);
        parent::tearDown();
    }

    /** @test */
    public function it_uploads_a_file_in_root_directory()
    {
        // Given there is a disk called 'integration_tests'.

        // And it has the usual directory structure.

        // And have a local file ready to upload
        $localFile = env('BASE_PATH') . 'tests/api/stubs/files/' . $this->testFile;

        $uploadedFile = new Symfony\Component\HttpFoundation\File\UploadedFile(
            $localFile,
            $this->testFile,
            'application/vnd.ms-excel',
            null,
            null,
            true
        );

        // When we upload the file
        $this->assertTrue(\App\DiskBrowser\File::uploadFile($uploadedFile, $this->testFile, $this->testDisk, '/'));

        // Then we see that the file exists in the root directory
        $this->assertTrue($this->doesExist('/' . $this->testFile));
    }

    /** @test */
    public function it_uploads_a_file_in_a_given_directory()
    {
        // Given there is a disk called 'integration_tests'.

        // And it has the usual directory structure.

        // And there is a test directory in the root directory
        \App\DiskBrowser\Directory::createDirectory($this->testDirectory, $this->testDisk, '/');

        // And have a local file ready to upload
        $localFile = env('BASE_PATH') . 'tests/api/stubs/files/' . $this->testFile;

        $uploadedFile = new Symfony\Component\HttpFoundation\File\UploadedFile(
            $localFile,
            $this->testFile,
            'application/vnd.ms-excel',
            null,
            null,
            true
        );

        // When we upload a file
        $this->assertTrue(\App\DiskBrowser\File::uploadFile($uploadedFile, $this->testFile, $this->testDisk, '/' . $this->testDirectory));

        // Then we see that the file exists in the given directory
        $this->assertTrue($this->doesExist('/' . $this->testDirectory . '/'. $this->testFile));
    }

    /** @test */
    public function it_generates_a_unique_name_for_a_file()
    {
        // Given there is a disk called 'integration_tests'.

        // And it has the usual directory structure.

        $localFile = env('BASE_PATH') . 'tests/api/stubs/files/file_to_upload.jpg';

        $uploadedFile = new Symfony\Component\HttpFoundation\File\UploadedFile(
            $localFile,
            'file_to_upload.jpg',
            'application/vnd.ms-excel',
            null,
            null,
            true
        );

        // When we generate a unique name for a given local file
        $fileName = \App\DiskBrowser\File::generateUniqueFileName($uploadedFile);

        // We see that the unique file name contains the original file name
        $this->assertContains('file_to_upload', $fileName);

        // But it is not same as original file name
        $this->assertNotEquals('file_to_upload.jpg', $fileName);

        // And it contains the same extension as original file name
        $this->assertContains('.jpg', $fileName);

    }

    /** @test */
    public function it_returns_only_the_files_present_in_root_directory()
    {
        // Given there is a disk called 'integration_tests'.

        // And it has the usual directory structure.

        // When we get files in root of the given disk
        $result = \App\DiskBrowser\File::filesIn($this->testDisk, '/');

        // We see total 3 files
        $this->assertCount(3, $result);

        $expectations = [
            'i-love-this-dog.jpg',
            'my-cat.jpg',
            'my-dog.jpg',
        ];

        //Which have name, path, size and modified_at parameters
        for($i = 0; $i < sizeof($result); $i++) {
            $this->assertEquals($expectations[$i], $result[$i]);
        }
    }


    /** @test */
    public function it_returns_size_of_a_given_file()
    {
        // Given there is a disk called 'integration_tests'.

        // And it has the usual directory structure.

        $size = \App\DiskBrowser\File::size('/my-cat.jpg', $this->testDisk);

        $this->assertGreaterThan(0, $size);
    }

    /** @test */
    public function it_returns_last_modified_date_of_requested_file()
    {
        // Given there is a disk called 'integration_tests'.

        // And it has the usual directory structure.

        $date = \App\DiskBrowser\File::lastModified('/my-cat.jpg', $this->testDisk);

        $this->assertTrue($date < date('Y-m-d H:i:s'));
    }

    /** @test */
    public function it_returns_file_meta_data_for_a_requested_file()
    {
        // Given there is a disk called 'integration_tests'.

        // And it has the usual directory structure.
        
        $result = \App\DiskBrowser\File::metaDataOf('/my-cat.jpg', $this->testDisk);

        $this->assertEquals('my-cat.jpg', $result['name']);
        $this->assertEquals('/test/', $result['path']);
        $this->assertTrue($result['size'] >= 0);
        $this->assertTrue($result['modified_at'] < date('Y-m-d H:i:s'));

    }

    /** @test */
    public function it_returns_array_of_all_files_matching_a_given_word_in_a_given_disk()
    {
        // Given there is a disk called 'integration_tests'.

        // And it has the usual directory structure.

        // When we search for 'cute' word in all the files

        $result = \App\DiskBrowser\File::searchDisk('cute', $this->testDisk);

        $expectations = [
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
        ];

        // Then we see all the files having 'cute' word in their names
        $this->assertCount(4, $result);

        // Files have name, path, size and modified_at parameters
        for($i = 0; $i < sizeof($result); $i++) {
            $this->assertEquals($expectations[$i]['name'], $result[$i]['name']);
            $this->assertEquals($expectations[$i]['path'], $result[$i]['path']);
            $this->assertTrue($result[$i]['size'] >= 0);
            $this->assertTrue($result[$i]['modified_at'] < date('Y-m-d H:i:s'));
        }

    }

    /** @test */
    public function it_returns_list_of_files_including_the_file_which_matches_searched_word_but_has_a_different_case()
    {
        // Given there is a disk called 'integration_tests'.

        // And it has the usual directory structure.

        // When we search for 'CUte' word in all the files

        $result = \App\DiskBrowser\File::searchDisk('CUte', $this->testDisk);

        $expectations = [
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
        ];

        // Then we see all the files having 'cute' word in their names
        $this->assertCount(4, $result);

        // Files have name, path, size and modified_at parameters
        for($i = 0; $i < sizeof($result); $i++) {
            $this->assertEquals($expectations[$i]['name'], $result[$i]['name']);
            $this->assertEquals($expectations[$i]['path'], $result[$i]['path']);
            $this->assertTrue($result[$i]['size'] >= 0);
            $this->assertTrue($result[$i]['modified_at'] < date('Y-m-d H:i:s'));
        }
    }

    /** @test */
    public function it_returns_true_if_file_name_has_extension()
    {
        $this->assertTrue(\App\DiskBrowser\File::doesTheFileHaveExtension('i-love-this-dog.jpg'));
    }

    /** @test */
    public function it_returns_false_if_file_name_does_not_have_extension()
    {
        $this->assertFalse(\App\DiskBrowser\File::doesTheFileHaveExtension('i-love-this-dog'));

        $this->assertFalse(\App\DiskBrowser\File::doesTheFileHaveExtension('i-love-this-dog.'));
    }

    /** @test */
    public function it_returns_true_if_file_extension_is_allowed_on_given_disk()
    {
        $this->assertTrue(\App\DiskBrowser\File::isFileAllowedOnDisk('/i-love-this-dog.jpg', 'integration_tests'));
    }

    /** @test */
    public function it_returns_false_if_file_extension_is_not_allowed_on_given_disk()
    {
        $this->assertFalse(\App\DiskBrowser\File::isFileAllowedOnDisk('/i-love-this-dog.xlsx', 'integration_tests'));

        $this->assertFalse(\App\DiskBrowser\File::isFileAllowedOnDisk('/i-love-this-dog.doc', 'integration_tests'));

        $this->assertFalse(\App\DiskBrowser\File::isFileAllowedOnDisk('/.DS_Store', 'integration_tests'));
    }

    /** @test */
    public function it_returns_true_if_file_is_a_hidden_file()
    {
        $this->assertTrue(\App\DiskBrowser\File::isGivenFileHidden('.DS_Store'));
    }

    /** @test */
    public function it_returns_false_if_file_is_not_a_hidden_file()
    {
        $this->assertFalse(\App\DiskBrowser\File::isGivenFileHidden('i-love-this-dog.png'));
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
     * Delete a given file in root directory
     * @param string $file
     */
    private function deleteFile($file)
    {
        if ($this->doesExist($file)) {
            Storage::disk('integration_tests')->delete($this->testFile);
        }
    }

}
