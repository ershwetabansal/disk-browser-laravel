tinymce.init({
        selector: 'textarea#tinyMCE',
        paste_as_text: true,
        plugins: [
            "advlist autolink link image imagetools lists anchor code fullscreen table template paste"
        ],

        toolbar1: "bold italic underline | alignleft aligncenter alignright alignjustify | bullist numlist | outdent indent | undo redo",
        toolbar2: "table link unlink | image | formatselect | code | DiskBrowser",
        toolbar_items_size: 'small',
        menubar: false,
        paste_data_images: false,
        file_browser_callback : tinmyceDiskBrowser,
        setup : function(editor) {
            editor.addButton('DiskBrowser', {
              text: 'Disk Browser',
              icon: false,
              onclick: function () {
                browser.openBrowser({
                    disks : [
                        'Images', 'Documents'
                    ],
                    button : {
                        text : 'Update URL',
                        onClick : function(url) {
                            editor.insertContent('<img src="'+url+'"/>');
                        }
                    }
                });
              }
            });
        }
    });

function tinmyceDiskBrowser(field_id, url, type, win)
{
    browser.openBrowser({
        disks : [
            'Images', 'Documents'
        ],
        button : {
            text : 'Update URL',
            onClick : function(path) {
                win.document.getElementById(field_id).value = path;
            }
        }
    });
}