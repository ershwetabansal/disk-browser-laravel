var browser = FileBrowser().getInstance();
browser.setup({

    disks : {
        search : true,
        search_URL: '/api/v1/disk/search',
        details : [
            {
                name: 'website-assets',
                label: 'Website assets',
                allowed_extensions: ['png','jpg','jpeg','bmp','tiff','gif']
            },
            {
                name: 'publications',
                label: 'Publication images',
                allowed_directories: ['/images'],
                allowed_extensions: ['png','jpg','jpeg','bmp','tiff','gif'],
                read_only: true
            },

            {
                name: 'website-assets',
                label: 'Service overviews',
                allowed_directories: ['/service_overviews'],
                allowed_extensions: ['pdf', 'doc', 'docx']
            }

        ]
    },
    directories: {
        list: '/api/v1/disk/directories',
        create: '/api/v1/disk/directory/store'
    },
    files: {
        list: '/api/v1/disk/files',
        upload: {
            url: '/api/v1/disk/file/store',
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
            var message = '';
            if (status == '422') {
                for (var key in response) {
                    if (typeof(response[key]) == 'object') {
                        message = message + response[key][0] + ' ';
                    }
                }
            }
            return (message == '') ? 'Error encountered. ' : message ;
        }
    },
    authentication : "session"
});

function tinmyceDiskBrowser(field_id, url, type, win)
{
    browser.openBrowser({
        disks : [
            'Website assets', 'Publication images'
        ],
        button : {
            text : 'Update URL',
            onClick : function(path) {
                win.document.getElementById(field_id).value = path;
            }
        }
    });
}

/**
 * Set up the callback and other config parameters on display of disk browser.
 *
 * @param callback
 * @param disks
 */
function accessBrowser(callback, disks)
{
    var configParameters = {
        button : {
            text : 'Update URL',
            onClick : function(path) {
                if (callback) callback(path);
            }
        }
    };

    if (disks) {
        configParameters.disks = getArrayFromCSV(disks);
    }

    browser.openBrowser(configParameters);
}

function getArrayFromCSV(csv)
{
    // Return empty array if csv is not defined
    if (!csv) {
        return [];
    }

    if (csv.indexOf(',')) {
        return csv.split(',');
    }

    return [csv];
}