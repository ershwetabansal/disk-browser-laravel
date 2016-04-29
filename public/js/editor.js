
tinymce.init({
    selector: 'textarea',
    paste_as_text: true,
    plugins: [
        "advlist autolink link image imagetools lists anchor code fullscreen table template paste"
    ],
    //
    toolbar1: "bold italic underline | alignleft aligncenter alignright alignjustify | bullist numlist | outdent indent | undo redo",
    toolbar2: "table link unlink | image | formatselect | code",
    menubar: false,
    file_browser_callback : myFileBrowser
});


var browser = FileBrowser().getInstance();
browser.setup({

    disks : {
        search : true,
        details : [
            {
                //In case of cross origin disk
                name: 'ea_images',
                label: 'Images',
                search_URL: '/asset/file/search',
                path : {
                    relative : true
                }
            },
            {
                //In case of cross origin disk
                name: 'ea_publications',
                label: 'Publications',
                search_URL: '/asset/file/search',
                path : {
                    relative : true
                }
            }
            //{
            //    //For managing the same server folder
            //    name: 'publications',
            //    label: 'Publications',
            //    search_URL: '/asset/file/search',
            //    path : {
            //        root : 'http://image-upload.com'
            //    }
            //},
            //
            //   {
            //       //For getting root paths in session
            //       name: 'general',
            //       label: 'General',
            //       search_URL: '/asset/file/search',
            //       path : {
            //           relative : false,
            //           cookie : 'root_path'
            //       }
            //   },
            //   {
            //    //For managing a third party disk with no absolute path
            //    name: 'S3',
            //    label: 'AWS S3',
            //    search_URL: '/asset/file/search',
            //    path : {
            //        relative : false,
            //        absolute : false
            //    }
            //}

        ]
    },
    directories: {
        list: '/api/v1/directories',
        //destroy: '/api/v1/directory/destroy',
        create: '/api/v1/directory/store'
        //update: '/api/v1/directory/update'
    },
    files: {
        list: '/api/v1/files',
        //destroy: '/api/v1/file/destroy',
        upload: {
            url: '/api/v1/file/store',
            params: [
                //{
                //    name: 'name',
                //    label: 'File Name'
                //}
            ]
        },
        thumbnail: {
            show : true,
            directory : '/thumbnails',
            path : '',
            prefix : '',
            suffix : ''
        },
        update: '/asset/file/store',
        size_unit : 'KB'
    },
    http : {
        headers : {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    },
    authentication : "session"
});
function myFileBrowser(field_id, url, type, win)
{
    browser.openBrowser({
        button : {
            text : 'Update URL',
            path : url,
            onClick : function(path) {
                win.document.getElementById(field_id).value = path;
            }
        }
    });
}

function accessBrowser()
{
    browser.openBrowser({
        button : {
            text : 'Update URL',
            onClick : function(path) {
                console.log("path :"+path);
            }
        }
    });
}

/*
 var s3 = {
 type: 'S3',
 name: 'S3',
 absolute_paths: false,
 files: {
 list: '/asset/files',
 destroy: '/asset/file/destroy',
 create: '/asset/file/store',
 units: 'kb'
 },
 directories: {
 list: '/asset/directories',
 destroy: '/asset/directories/destroy',
 create: '/asset/directories/store'
 }
 }

 var spreadsheet = {
 extensions: ['xls', 'xlsx'],
 callback: 'functionName',

 }

 var categories = [45, 137, 86];

 var image = {
 extensions: ['jpg'],
 onSave: 'someOtherFunction',
 onRename: 'generateThumbnails',
 meta_attributes: [
 {
 name: 'Name',
 required: true,
 type: 'text'
 },
 {
 name: 'Categories',
 required: false,
 type: 'array',
 uses: categories
 }
 ]
 }*/
