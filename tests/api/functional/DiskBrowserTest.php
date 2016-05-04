<?php

use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class FileBrowserTest extends TestCase
{

    use DatabaseTransactions;

    private $testDirectory = 'my_test_directory';

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

    public function it_prevents_use_by_guests()
    {
        $this->be(null);

        $this->post('whatever')->assertResponseStatus(401);

        // Assert errors etc whatever

        $this->be(factory(\App\User::class)->create());

        $this->post('whatever')->assertResponseStatus(200);
        
    }

    /** @test */
    public function it_returns_directories_from_a_given_disks_root()
    {
        // Given there is a disk called 'integration_tests'.

        // And it has the usual directory structure.


        // When I make a POST request to /api/v1/directories
        $this->post('/api/v1/directories', ['disk' => 'integration_tests'])

                // Then I see the directories are as follows
                ->seeJson([
                    'name' => 'cats',
                    'path' => 'cats',
                ])
                ->seeJson([
                    'name' => 'monkeys',
                    'path' => 'monkeys'
                ])->seeJson([
                    'name' => 'dogs',
                    'path' => 'dogs'
                ])

                // And there are only three directories returned
                ->assertCount(3, json_decode($this->response->content()));
    }

    /** @test */
    public function it_returns_directories_within_a_specific_directory()
    {
        // Given there is a disk called 'integration_tests'.

        // And it has the usual directory structure.

        // When I make a POST request to /api/v1/directories with the '/monkeys' path.
        $this->post('/api/v1/directories', ['disk' => 'integration_tests', 'path' => '/monkeys'])
            ->seeJson([
                'name' => 'angry',
                'path' => 'monkeys/angry'
            ])
            ->seeJson([
                'name' => 'cute',
                'path' => 'monkeys/cute'
            ])

            // And there are only two directories returned.
            ->assertCount(2, json_decode($this->response->content()));

    }

    /** @test */
    public function it_returns_directories_within_the_subdirectory_of_another_directory()
    {
        // Given there is a disk named 'integration_tests'.

        // And it has the usual directory structure.

        // When I make a POST request to /api/v1/directories with the '/dogs/puppies' path
        $this->post('/api/v1/directories', ['disk' => 'integration_tests', 'path' => '/dogs/puppies'])

            // Then I see the following directory
            ->seeJson([
                'name' => 'trained',
                'path' => 'dogs/puppies/trained',
            ])

            // And there is only one directory returned.
            ->assertCount(1, json_decode($this->response->content()));
    }

    /** @test */
    public function it_returns_a_list_of_files_in_the_root_directory_of_a_given_disk()
    {
        // Given there is a disk named 'integration_tests'.

        // And it has the usual directory structure.

        // When I make a POST request to /api/v1/files
        $this->post('/api/v1/files', ['disk' => 'integration_tests'])

            //Then I see the following files
            ->seeJson([
                'name' => 'i-love-this-dog.jpg',
                'path' => '/i-love-this-dog.jpg',
                'size' => 0,
                'modified_at' => '2016-05-04 13:58:11',
            ])
            ->seeJson([
                'name' => 'my-dog.jpg',
                'path' => '/my-dog.jpg',
                'size' => 0,
                'modified_at' => '2016-05-04 13:58:11',
            ])
            ->seeJson([
                'name' => 'my-cat.jpg',
                'path' => '/my-cat.jpg',
                'size' => 0,
                'modified_at' => '2016-05-04 13:58:11',
            ])

            // And there are total three files returned.
            ->assertCount(3, json_decode($this->response->content()));

    }

    /** @test */
    public function it_returns_a_list_of_files_in_a_given_directory_of_a_given_disk()
    {

        // Given there is a disk named 'integration_tests'.

        // And it has the usual directory structure.

        // When I make a POST request to /api/v1/files
        $this->post('/api/v1/files', ['disk' => 'integration_tests', 'path' => '/cats'])

            //Then I see the following files
            ->seeJson([
                'name' => 'fat_cat.png',
                'path' => '/cats/fat_cat.png',
                'size' => 0,
                'modified_at' => '2016-05-04 15:32:19',
            ])

            // And there is total one file returned.
            ->assertCount(1, json_decode($this->response->content()));

    }

    /** @test */
    public function it_returns_a_list_of_files_in_a_subdirectory_of_another_directory()
    {

        // Given there is a disk named 'integration_tests'.

        // And it has the usual directory structure.

        // When I make a POST request to /api/v1/files
        $this->post('/api/v1/files', ['disk' => 'integration_tests', 'path' => '/dogs/puppies/trained'])

            //Then I see the following files
            ->seeJson([
                'name' => 'cute_and_trained_puppies.jpg',
                'path' => '/dogs/puppies/trained/cute_and_trained_puppies.jpg',
                'size' => 0,
                'modified_at' => '2016-05-04 15:21:57',
            ])
            ->seeJson([
                'name' => 'trained_puppies.jpg',
                'path' => '/dogs/puppies/trained/trained_puppies.jpg',
                'size' => 0,
                'modified_at' => '2016-05-04 15:21:46',
            ])

            // And there are total two files returned.
            ->assertCount(2, json_decode($this->response->content()));

    }

    /** @test */
    public function it_can_create_a_directory_within_the_root_directory_of_a_given_disk()
    {

        // Given there is a disk named 'integration_tests'.

        // And it has the usual directory structure.

        // When I make a POST request to /api/v1/directory/store with a directory name
        $this->post('/api/v1/directory/store', ['disk' => 'integration_tests', 'name' => $this->testDirectory])

            //Then I see success as true and the name and path for new directory
            ->seeJson([
                'success' => true,
                'directory' => [
                    'name' => $this->testDirectory,
                    'path' => '/' . $this->testDirectory,
                ]
            ]);

        $this->assertTrue($this->doesExist('/' . $this->testDirectory));

    }



    public function it_can_create_a_directory_within_a_given_directory_of_a_given_disk()
    {
        //When I hit the create directory API, it should create a new directory

        //When I pass only the disk, it should create directory inside root directory
        $this->post('/api/v1/directory/store', ['disk' => 'integration_tests', 'name' => 'elephants'])
            ->seeJson([
                'success' => true,
                'directory' => [
                    'name' => 'elephants',
                    'path' => '',
                ]
            ]);

        $this->assertTrue($this->doesExist('/test'));

        //If I pass 'path' for first level directory, it should create directory inside first level directory
        $this->post('/api/v1/directory/store', ['disk' => 'integration_tests', 'path' => '/test', 'name' => '2016'])
            ->seeJson([
                'success' => true
            ]);

        $this->assertTrue($this->doesExist('/test/2016'));

        //If I pass 'path' for second level directory, it should create directory inside second level directory
        $this->post('/api/v1/directory/store', ['disk' => 'integration_tests', 'path' => '/test/2016', 'name' => '01'])
            ->seeJson([
                'success' => true
            ]);

        $this->assertTrue($this->doesExist('/test/2016/01'));

    }

    /** @test */
    public function it_does_not_allow_a_directory_to_be_created_if_a_directory_with_the_same_name_already_exists()
    {
        //When I hit the create directory API, it should create a new directory

        $this->post('/api/v1/directory/store', ['disk' => 'integration_tests', 'name' => 'my-new-folder'])
            ->seeJson([
                'success' => true,
                'directory' => [
                    'name' => 'my-new-folder',
                    'path' => ' as saf a',
                ]
            ])
        ;

        $this->post('/api/v1/directory/store', ['disk' => 'integration_tests', 'name' => 'my-new-folder'])
            ->seeJson([
                'success' => false,
                'errors' => [
                    'A directory already exists with this name',
                ]
            ])->assertResponseStatus(422);

    }
    /** @test */
    public function it_stores_a_file_in_the_root_directory_of_a_given_disk()
    {
        //Upload a local file to disk 'integration_tests'
        $localFile = env('BASE_PATH') . 'tests/stubs/files/test.jpg';

        $uploadedFile = new Symfony\Component\HttpFoundation\File\UploadedFile(
            $localFile,
            'test.jpg',
            'image/jpeg',
            null,
            null,
            true
        );

        $response = $this->call('POST', '/api/v1/file/store', [
            'disk' => 'integration_tests',
            'path' => '/upload'
        ],
            [],
            ['file' => $uploadedFile]
        );

        //Response should contain path, name, size and last modified date of the uploaded file
        $this->assertContains('name', $response->content());
        $this->assertContains('path', $response->content());
        $this->assertContains('size', $response->content());
        $this->assertContains('last_modified_date', $response->content());

        //Uploaded file should exist physically
        $this->doesExist($response->content()['path']);

        //Uploaded file name should contain the slugified version of original file name partly
        $this->assertContains(str_slug(preg_replace('/\\.[^.\\s]{3,4}$/', '', $uploadedFile->getClientOriginalName())),
            $response->getContent()->name);

        //Uploaded file name should have a random suffix

        $this->assertEquals('/[a-zA-z0-9]/', array_reverse(explode('_', $response->getContent()['name']))[0]);

        //Uploaded file name should have the extension same as that of the uploaded file
        $this->assertContains($uploadedFile->getClientOriginalExtension(), $response->getContent()['name']);

    }

    /** @test */
    public function it_should_not_store_file_in_a_directory_if_extension_is_not_from_allowed_extensions_list()
    {
        //Upload a local file to disk 'integration_tests' with mimeType other than 'jpeg/png'
        $localFile = env('BASE_PATH') . 'tests/stubs/files/spreadsheet.xlsx';

        $uploadedFile = new Symfony\Component\HttpFoundation\File\UploadedFile(
            $localFile,
            'spreadsheet.xlsx',
            'application/vnd.ms-excel',
            null,
            null,
            true
        );

        $response = $this->call(
            'POST',
            '/api/v1/file/store',
            ['disk' => 'integration_tests', 'path' => '/upload'],
            [],
            ['file' => $uploadedFile],
            [
                'X-Content-Type-Options' => 'application/json',
                'HTTP_X_REQUESTED_WITH' => 'XMLHttpRequest',
            ]
        );

        //Response should have status 422 and error should say 'File type is not allowed
        $this->seeJsonContains([
            'file' => ['The file must be a file of type: jpeg, png.']
        ]);
        $this->assertEquals('422', $response->getStatusCode());

    }

    /** @test */
    public function it_searches_files_and_directories_in_a_given_disk()
    {
       // Given there is a disk called 'integration_tests'

       // And the disk has the following structure:

       /*
        | .
        |  /images
        |       1461869658-41737717-graph002.png
        |       /graphs
        |           99999-graph003.png
        |  /publications
        |       /2016
        |           /01
        |               55555-graph001.png
        |  1461869658-78982514-graph004.png
        |
        */

        // When I search for 'cat'
        // Then I see:

        $result = [
            'directories' => [
                '/images/graphs'
            ],
            'files' => [
                [
                    'name' => '1461869658-41737717-graph002.png',
                    'path' => '/images/1461869658-41737717-graph002.png'
                ],
                [
                    'name' => '55555-graph001.png',
                    'path' => '/publications/2016/01/55555-graph001.png'
                ],
                [
                    'name' => '1461869658-78982514-graph004.png',
                    'path' => '/1461869658-78982514-graph004.png'
                ],
                [
                    'name' => '99999-graph003.png',
                    'path' => '/images/graphs/99999-graph003.png'
                ]
            ],
        ];

        $this->post('/api/v1/disk/search', ['disk' => 'integration_tests', 'search' => 'graph'])
            ->seeJson($result)
        ;

        $this->post('/api/v1/disk/search', ['disk' => 'integration_tests', 'search' => 'test'])
            ->seeJson([])
        ;
    }


    /**
     * Delete all the files inside a directory
     */
    private function clearUploadedFiles()
    {
        Storage::disk('integration_tests')->deleteDirectory('upload');
        Storage::disk('integration_tests')->makeDirectory('upload');
    }

    /**
     * Delete a given directory
     * @param string $directory
     */
    private function deleteDirectory($directory)
    {
        Storage::disk('integration_tests')->deleteDirectory($directory);
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

    // It can create a new directory in a given directory
    // It can store a file in a given directory

    // Integration
    // It doesn't allow a directory to be created with the same name as another in a given directory

    // Unit
    // It generates a unique file name when uploading a file

}
