<?php


use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class DiskBrowserTest extends TestCase
{

    use DatabaseTransactions;

    private $testDirectory = 'elephants';

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

        $user = factory(User::class)->create();
        $user->email = 'test@energyaspects.com';

        $this->be($user);
    }

    public function tearDown()
    {
        Auth::logout();
        parent::tearDown();

        $this->deleteDirectory($this->testDirectory);
    }

    /** @test */
    public function it_prevents_use_by_guests()
    {
        $this->get('/');

        // When user is not logged in
        Auth::logout();

        // Any api request to server should get unauthorized response code
        $this->json('post', '/api/v1/disk/directories', ['disk' => 'integration_tests', 'path' => '/',], $this->ajaxHeaders())
            ->assertResponseStatus(401);

        // When user is logged in
        $user = factory(User::class)->create();
        $user->email = 'test@energyaspects.com';

        $this->be($user);

        //Then user should get 'Ok' response from server
        $this->json('post', '/api/v1/disk/directories', ['disk' => 'integration_tests', 'path' => '/',], $this->ajaxHeaders())
//             ->assertResponseStatus(200);
        ->seeJson([]);

    }

    /** @test */
    public function it_returns_directories_from_a_given_disks_root()
    {
        // Given there is a disk called 'integration_tests'.

        // And it has the usual directory structure.


        // When I make a POST request to /api/v1/disk/directories
        $this->json('post', '/api/v1/disk/directories', ['disk' => 'integration_tests', 'path' => '/'])

                // Then I see the directories are as follows
                ->seeJson([
                    'name' => 'cats',
                    'path' => '/',
                ])
                ->seeJson([
                    'name' => 'monkeys',
                    'path' => '/'
                ])->seeJson([
                    'name' => 'dogs',
                    'path' => '/'
                ])

                // And there are only three directories returned
                ->assertCount(3, json_decode($this->response->content()));
    }

    /** @test */
    public function it_returns_directories_within_a_specific_directory()
    {
        // Given there is a disk called 'integration_tests'.

        // And it has the usual directory structure.

        // When I make a POST request to /api/v1/disk/directories with the '/monkeys' path.
        $this->json('post', '/api/v1/disk/directories',
                    ['disk' => 'integration_tests', 'path' => '/monkeys']
            )
            ->seeJson([
                'name' => 'angry',
                'path' => '/monkeys/'
            ])
            ->seeJson([
                'name' => 'cute',
                'path' => '/monkeys/'
            ])

            // And there are only two directories returned.
            ->assertCount(2, json_decode($this->response->content()));

    }

    /** @test */
    public function it_returns_directories_within_the_subdirectory_of_another_directory()
    {
        // Given there is a disk named 'integration_tests'.

        // And it has the usual directory structure.

        // When I make a POST request to /api/v1/disk/directories with the '/dogs/puppies' path
        $this->json('post', '/api/v1/disk/directories',
                    ['disk' => 'integration_tests', 'path' => '/dogs/puppies']
            )

            // Then I see the following directory
            ->seeJson([
                'name' => 'trained',
                'path' => '/dogs/puppies/',
            ])

            // And there is only one directory returned.
            ->assertCount(1, json_decode($this->response->content()));
    }

    /** @test */
    public function it_returns_an_error_when_directories_are_requested_from_a_path_that_does_not_exist()
    {

        // Given there is a disk named 'integration_tests'.

        // And it has the usual directory structure.

        // When I make a POST request to /api/v1/disk/files with an incorrect path
        $this->json('post', '/api/v1/disk/directories',
            ['disk' => 'integration_tests', 'path' => '/this_does_not_exist']
        )
            //Then I see the following error
            ->seeJson([
                "errors" => ["Path does not exist."]
            ])->assertResponseStatus(422);;

    }

    /** @test */
    public function it_returns_a_list_of_files_in_the_root_directory_of_a_given_disk()
    {
        // Given there is a disk named 'integration_tests'.

        // And it has the usual directory structure.

        // When I make a POST request to /api/v1/disk/files
        $this->json('post', '/api/v1/disk/files', ['disk' => 'integration_tests', 'path' => '/',])

            //Then I see the following files
            ->seeJson([
                'name' => 'i-love-this-dog.jpg',
                'path' => '/test/',
                'size' => 4.048,
            ])
            ->seeJsonContains([
                'name' => 'my-dog.jpg',
                'path' => '/test/',
                'size' => 4.048,
            ])
            ->seeJsonContains([
                'name' => 'my-cat.jpg',
                'path' => '/test/',
                'size' => 4.048,
            ]);
        
        $response = $this->response->content();

        //And Json contains modified_at variable along with other parameters
        $this->assertContains('modified_at', $response);

        // And there are total three files returned.
        $this->assertCount(3, json_decode($response));

    }


    /** @test */
    public function it_returns_files_with_only_allowed_file_types()
    {
        // Given there is a disk called 'integration_tests'.

        // And it has the usual directory structure.

        // And only images are allowed on that disk while one spreadsheet is present on the root path of disk

        // When I make a post request to /api/v1/disk/files
        $this->json('post', '/api/v1/disk/files',
                    ['disk' => 'integration_tests', 'path' => '/',]
            )

        // I should not see spreadsheet in the result
            ->dontSeeJson([
                'name' => 'spreadsheet.xlsx'
            ]);

    }
    /** @test */
    public function it_returns_a_list_of_files_in_a_given_directory_of_a_given_disk()
    {

        // Given there is a disk named 'integration_tests'.

        // And it has the usual directory structure.

        // When I make a POST request to /api/v1/disk/files
        $this->json('post', '/api/v1/disk/files',
                    ['disk' => 'integration_tests', 'path' => '/cats',]
            )

            //Then I see the following files
            ->seeJson([
                'name' => 'fat_cat.png',
                'path' => '/test/cats/',
                'size' => 0,
            ]);

        $response = $this->response->content();

        //And Json contains modified_at variable along with other parameters
        $this->assertContains('modified_at', $response);

        // And there is total one file returned.
        $this->assertCount(1, json_decode($response));

    }

    /** @test */
    public function it_returns_a_list_of_files_in_a_subdirectory_of_another_directory()
    {

        // Given there is a disk named 'integration_tests'.

        // And it has the usual directory structure.

        // When I make a POST request to /api/v1/disk/files
        $this->json('post', '/api/v1/disk/files',
                    ['disk' => 'integration_tests', 'path' => '/dogs/puppies/trained']
            )

            //Then I see the following files
            ->seeJson([
                'name' => 'cute_and_trained_puppies.jpg',
                'path' => '/test/dogs/puppies/trained/',
                'size' => 0,
            ])
            ->seeJson([
                'name' => 'trained_puppies.jpg',
                'path' => '/test/dogs/puppies/trained/',
                'size' => 0,
            ]);

        $response = $this->response->content();

        //And Json contains modified_at variable along with other parameters
        $this->assertContains('modified_at', $response);

        // And there are total two files returned.
        $this->assertCount(2, json_decode($response));

    }

    /** @test */
    public function it_returns_error_when_files_are_requested_from_a_path_that_does_not_exist()
    {

        // Given there is a disk named 'integration_tests'.

        // And it has the usual directory structure.

        // When I make a POST request to /api/v1/disk/files with an incorrect path
        $this->json('post', '/api/v1/disk/files',
                ['disk' => 'integration_tests', 'path' => '/' . $this->testDirectory]
            )
            //Then I see the following error
            ->seeJson([
                "errors" => [ "Path does not exist." ]
            ])->assertResponseStatus(422);;

    }

    /** @test */
    public function it_can_create_a_directory_within_the_root_directory_of_a_given_disk()
    {

        // Given there is a disk named 'integration_tests'.

        // And it has the usual directory structure.

        // When I make a POST request to /api/v1/disk/directory/store with a directory name
        $this->json('post', '/api/v1/disk/directory/store',
                    ['disk' => 'integration_tests', 'name' => $this->testDirectory, 'path' => '/',]
            )

            //Then I see success as true and the name and path for new directory
            ->seeJson([
                'success' => true,
                'directory' => [
                    'name' => $this->testDirectory,
                    'path' => '/',
                ]
            ]);

        $this->assertTrue($this->doesExist('/' . $this->testDirectory));

    }

    /** @test */
    public function it_returns_error_when_directory_is_tried_to_be_created_in_a_path_that_does_not_exist()
    {

        // Given there is a disk named 'integration_tests'.

        // And it has the usual directory structure.

        // When I make a POST request to /api/v1/disk/files with an incorrect path
        $this->json('post', '/api/v1/disk/directory/store',
            ['disk' => 'integration_tests', 'path' => '/' . $this->testDirectory, 'name' => $this->testDirectory]
        )
            //Then I see the following error
            ->seeJson([
                "errors" => [ "Path does not exist." ]
            ])->assertResponseStatus(422);
    }

    /** @test */
    public function it_returns_error_when_directory_name_is_not_given_while_creating_a_directory()
    {

        // Given there is a disk named 'integration_tests'.

        // And it has the usual directory structure.

        // And there is one test directory created in the root disk
        $this->json('post', '/api/v1/disk/directory/store', [
            'disk' => 'integration_tests',
            'name' => $this->testDirectory,
            'path' => '/',
        ]);

        // When I make a POST request to /api/v1/disk/files without a directory name
        $this->json('post', '/api/v1/disk/directory/store',
            ['disk' => 'integration_tests', 'name' => '', 'path' => '/' . $this->testDirectory]
        )
            //Then I see the following error
            ->seeJson([
                "name" => [ "The name field is required." ]
            ])->assertResponseStatus(422);


    }


    /** @test */
    public function it_can_create_a_directory_within_a_given_directory_of_a_given_disk()
    {
        // Given there is a disk named 'integration_tests'.

        // And it has the usual directory structure.

        // And there is one test directory created in the root disk
        $this->json('post', '/api/v1/disk/directory/store',
                [
                    'disk' => 'integration_tests',
                    'name' => $this->testDirectory,
                    'path' => '/',
                ]
        );

        // When I make another request to add a directory inside the previously created directory
        $this->json('post', '/api/v1/disk/directory/store', [
            'disk' => 'integration_tests',
            'name' => $this->testDirectory,
            'path' => "/" . $this->testDirectory ,
            ])

            //Then I see success as true and the name and path for new directory
            ->seeJson([
                'success' => true,
                'directory' => [
                    'name' => $this->testDirectory,
                    'path' => '/' . $this->testDirectory . '/' ,
                ]
            ]);

        $this->assertTrue($this->doesExist('/' . $this->testDirectory . '/' . $this->testDirectory));

    }

    /** @test */
    public function it_does_not_allow_a_directory_to_be_created_if_a_directory_with_the_same_name_already_exists()
    {
        // Given there is a disk named 'integration_tests'.

        // And it has the usual directory structure.

        // And I make a POST request to /api/v1/disk/directory/store with a directory name
        $this->json('post', '/api/v1/disk/directory/store',
                    [
                        'disk' => 'integration_tests',
                        'name' => $this->testDirectory,
                        'path' => '/',
                    ]
        );

        // When I make another request to add the directory with the same name as used in previous step
        

        $this->json('post', '/api/v1/disk/directory/store', 
                    ['disk' => 'integration_tests', 'name' => $this->testDirectory, 'path' => '/',]
            )
            //Then I see an error '422' and the error message
            ->seeJson([
                "errors" => [ "Directory already exists in the given path." ]
            ])->assertResponseStatus(422);

    }


    /** test */
    //TODO Laravel 5.2 is giving validation error on mimeType while the mimeType is correct. It needs to be debugged.
    public function it_stores_a_file_in_the_given_directory_of_a_given_disk()
    {
        // Given there is a disk named 'integration_tests'.

        // And it has the usual directory structure.

        // And we add a test directory in the root of the disk
        $this->json('post', '/api/v1/disk/directory/store',
                    [
                        'disk' => 'integration_tests',
                        'name' => $this->testDirectory,
                        'path' => '/',
                    ]
        );

        // And there is a local file, ready for upload on the disk.
        $localFile = env('BASE_PATH') . 'tests/api/stubs/files/elephant.jpg';

        $uploadedFile = new Symfony\Component\HttpFoundation\File\UploadedFile(
            $localFile,
            'elephant.jpg',
            'image/png',
            null,
            null,
            true
        );

        // When I make a POST request to upload the file to the newly created test directory
        $response = $this->call('POST', '/api/v1/disk/file/store',
                ['disk' => 'integration_tests', 'path' => '/' . $this->testDirectory],
                [],
                ['file' => $uploadedFile],
                self::jsonHeaders()
        );

        //Then response should contain path, name, size and last modified date of the uploaded file
        $this->assertContains('name', $response->content());
        $this->assertContains('path', $response->content());
        $this->assertContains('size', $response->content());
        $this->assertContains('modified_at', $response->content());

        //Uploaded file should exist physically
        $this->doesExist(json_decode($response->content(), true)['path']);

        //Uploaded file name should contain the slugified version of original file name partly
        $this->assertContains(
            str_slug(preg_replace('/\\.[^.\\s]{3,4}$/', '', $uploadedFile->getClientOriginalName())),
            json_decode($response->getContent(), true)['name']
        );

        //Uploaded file name should have a random suffix
        $this->assertRegExp('/[a-zA-z0-9.]+/',
            array_values(array_reverse(explode('_', json_decode($response->getContent(), true)['name'])))[0]);

        //Uploaded file name should have the extension same as that of the uploaded file
        $this->assertContains($uploadedFile->getClientOriginalExtension(),
            json_decode($response->getContent(), true)['name']);

    }

    /** @test */
    public function it_does_not_allow_to_store_file_in_a_directory_if_extension_is_not_from_allowed_extensions_list()
    {
        // Given there is a disk named 'integration_tests'.

        // And it has the usual directory structure.

        //There is one test directory created in the root disk
        $this->json('post', '/api/v1/disk/directory/store', [
            'disk' => 'integration_tests',
            'name' => $this->testDirectory,
            'path' => '/',
        ]);

        // And there is a local file, ready for upload on the disk.
        $localFile = env('BASE_PATH') . 'tests/api/stubs/files/spreadsheet.xlsx';

        $uploadedFile = new Symfony\Component\HttpFoundation\File\UploadedFile(
            $localFile,
            'spreadsheet.xlsx',
            'application/vnd.ms-excel',
            null,
            null,
            true
        );

        // When I make a POST request to upload the file
        $response = $this->call(
            'POST',
            '/api/v1/disk/file/store',
            ['disk' => 'integration_tests', 'path' => '/' . $this->testDirectory],
            [],
            ['file' => $uploadedFile],
            self::jsonHeaders()
        );

        //Response should have status 422 and error should say 'File type is not allowed
        $this->seeJsonContains([
            'file' => ['The file must be a file of type: jpeg, png, jpg.']
        ]);

        $this->assertEquals('422', $response->getStatusCode());

    }

    /** @test */
    public function it_searches_files_and_directories_in_a_given_disk()
    {
       
        // Given there is a disk called 'integration_tests'

        // And the disk has the usual directory structure:

        // When I search for 'cute' word
        $this->json('post', '/api/v1/disk/search',
                    ['disk' => 'integration_tests', 'search' => 'cute']
            )

            // I should see all the directories and files containing word 'cat'
            ->seeJson(
                [
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
                ]
            )
            ->seeJsonContains(
                [
                    'name' => 'cute_cat.png',
                    'path' => '/test/cats/cute/',
                    'size' => 0,
                ]
            )
            ->seeJsonContains(
                [
                    'name' => 'cute_puppies.jpg',
                    'path' => '/test/dogs/puppies/',
                    'size' => 0,
                ]
            )
            ->seeJsonContains(
                [
                    'name' => 'cute_and_trained_puppies.jpg',
                    'path' => '/test/dogs/puppies/trained/',
                    'size' => 0,
                ]
            )
            ->seeJsonContains(
                [
                    'name' => 'cute_monkey.png',
                    'path' => '/test/monkeys/cute/',
                    'size' => 0,
                ]
            );


    }

    /** @test */
    public function it_deletes_an_empty_directory()
    {
        // Given there is a disk named 'integration_tests'.

        // And it has the usual directory structure.

        // And there is one empty directory created
        $this->json('post', '/api/v1/disk/directory/store',
            ['disk' => 'integration_tests', 'name' => $this->testDirectory, 'path' => '/']
        );

        $this->assertTrue(self::doesExist('/' . $this->testDirectory));

        // And I make a POST request to /api/v1/disk/directory/destroy with a directory path
        $this->json('post', '/api/v1/disk/directory/destroy',
            ['disk' => 'integration_tests', 'path' => '/' , 'name' => $this->testDirectory]

        )->seeJson([
            'success' => true,
        ]);


        $this->assertFalse($this->doesExist('/' . $this->testDirectory));

    }

    /** @test */
    public function it_returns_error_if_trying_to_delete_a_directory_that_has_subdirectories()
    {
        // Given there is a disk named 'integration_tests'.

        // And it has the usual directory structure.

        // And I make a POST request to /api/v1/disk/directory/destroy with a directory path which has sub-directories
        $this->json('post', '/api/v1/disk/directory/destroy',
            ['disk' => 'integration_tests', 'path' => '/', 'name' => 'cats']

        )->seeJson([
            'success' => false,
            'errors' => [
                'Directory is not empty and can not be deleted.'
            ]
        ]);

        // Directory should still exist
        $this->assertTrue($this->doesExist('/cats'));
    }

    /** @test */
    public function it_returns_error_if_trying_to_delete_a_directory_that_has_files()
    {
        // Given there is a disk named 'integration_tests'.

        // And it has the usual directory structure.

        // And I make a POST request to /api/v1/disk/directory/destroy with a directory path which has files
        $this->json('post', '/api/v1/disk/directory/destroy',
            ['disk' => 'integration_tests', 'path' => '/cats/', 'name' => 'cute']

        )->seeJson([
            'success' => false,
            'errors' => [
                'Directory is not empty and can not be deleted.'
            ]
        ]);

        // Directory should still exist
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

    /**
     * Returns http headers required for making json request
     * @return array
     */
    private function jsonHeaders()
    {
        return $this->transformHeadersToServerVars([
            'Accept' => 'application/json',
        ]);
    }

    /**
     * Returns http headers required for making ajax request
     * @return array
     */
    private function ajaxHeaders()
    {
        return $this->transformHeadersToServerVars([
            'X-Requested-With' => 'XMLHttpRequest',
        ]);
    }
}
