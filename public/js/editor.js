
tinymce.init({
    selector: 'textarea',
    paste_as_text: true,
    plugins: [
        "advlist autolink link image lists anchor code fullscreen table template paste"
    ],
    //
    toolbar1: "bold italic underline | alignleft aligncenter alignright alignjustify | bullist numlist | outdent indent | undo redo",
    toolbar2: "table link unlink | image | formatselect | code",
    menubar: false,
    file_browser_callback : myFileBrowser,
    table_default_styles: {
        width: '100%'
    }
});


var browser = FileBrowser().getInstance();
browser.setup({

    disks : {
        search : true,
        search_URL: '/api/v1/disk/search',
        details : [
            {
                //In case of cross origin disk
                name: 'ea_images',
                label: 'Images',
                path : {
                    relative : true
                }
            },
            {
                //In case of cross origin disk
                name: 'ea_publications',
                label: 'Publications',
                path : {
                    relative : true
                }
            }
        ]
    },
    directories: {
        list: '/api/v1/directories',
        create: '/api/v1/directory/store'
    },
    files: {
        list: '/api/v1/files',
        upload: {
            url: '/api/v1/file/store',
            params:[]
        },
        thumbnail: {
            show : true,
            directory : '/thumbnails',
            path : '',
            prefix : '',
            suffix : ''
        },
        size_unit : 'KB'
    },
    http : {
        headers : {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        error : function(status, response) {
            console.log(response);
            if (status == '422') {
                for (var key in response) {
                    return response[key][0];
                }
            }
            return 'Error encountered. ';
        }
    },
    authentication : "session"
});

function myFileBrowser(field_id, url, type, win)
{
    browser.openBrowser({
        path : url,
        button : {
            text : 'Update URL',
            onClick : function(path) {
                win.document.getElementById(field_id).value = path;
            }
        }
    });
}

function accessBrowser()
{
    browser.openBrowser({
        context_menu: true,
        button : {
            text : 'Update URL',
            onClick : function(path) {
                console.log("path :"+path);
            }
        },
        resize : true
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
