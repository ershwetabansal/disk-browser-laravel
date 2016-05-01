<!DOCTYPE html>
<html>
<head lang="en">
    <meta charset="UTF-8">
    <title>File browser</title>
    <!-- Fonts -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.4.0/css/font-awesome.min.css" rel='stylesheet' type='text/css'>
    <link href="https://fonts.googleapis.com/css?family=Lato:100,300,400,700" rel='stylesheet' type='text/css'>

    <!-- Styles -->
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" rel="stylesheet">
    <link href="/app/build/css/app.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="http://code.jquery.com/ui/1.9.2/themes/base/jquery-ui.css">
</head>
<body>
<meta name="csrf-token" content="{{ csrf_token() }}">

<div class="text-center" style="margin-top: 10px;">
    <button type="button" class="btn btn-primary btn-lg" onclick="accessBrowser()">
        Launch demo modal
    </button>

    <div style="margin:0 auto; width: 70%;margin-top: 50px;">
        <textarea></textarea>
    </div>
</div>

<div class="modal fade file-manager" id="FileBrowser" tabindex="-1" role="dialog">
    <nav id="file-context-menu" class="context-menu hidden">
        <ul class="list-unstyled">
            <li><a href="#" id="view-file"><i class="fa fa-eye"></i> View</a></li>
            <li><a href="#" id="rename-file"><i class="fa fa-edit"></i> Rename</a></li>
            <li><a href="#" id="remove-file"><i class="fa fa-trash"></i> Remove</a></li>
            <li><a href="#" id="download-file"><i class="fa fa-download"></i> Download</a></li>
        </ul>
    </nav>

    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title pull-left" id="myModalLabel">File Manager</h4>
                <div role="button" class="pull-right" data-dismiss="modal">x</div>
            </div>
            <div class="modal-body">
                <div class="form-inline" role="toolbar" aria-label="...">
                    <div class="form-group" role="group" aria-label="...">
                        <select id="disk_selector" class="form-control">
                        </select>
                        <button class="btn btn-default" id="fb_create_new_directory">New Folder</button>
                        <button class="btn btn-default" id="upload_file_btn"><i class="fa fa-upload" aria-hidden="true"></i></button>

                        <button class="btn btn-default hidden desc" id="fb_file_manage">Manage &nbsp;<span class="caret"></span></button>
                    </div>


                    <div class="form-group pull-right">
                        <div class="btn-group" role="group" aria-label="...">
                            <button id="fb_refresh" class="btn btn-default" ><i class="fa fa-refresh"></i></button>
                            <button id="fb_align_list" class="btn btn-default" ><i class="fa fa-bars"></i></button>
                            <button id="fb_align_grid" class="btn btn-default" ><i class="fa fa-th"></i></button>
                        </div>

                        <select id="fb_sort_files" class="form-control" >
                            <option value="">Sort by</option>
                        </select>

                        <div class="input-group">
                            <input id="fb_search_input" type="text" class="form-control" id="filter" placeholder="Filter" />
                            <i id="fb_search_cancel" class="fa fa-times hidden"></i>
                            <div id="fb_search_submit" type="submit" role="button" class="input-group-addon">
                                <i class="fa fa-search"></i>
                            </div>
                        </div>
                    </div>

                </div>
                <div class="row">
                    <div class="col-md-3 col-xs-3 directories">

                        <ul class="list-unstyled" id="directories-list">
                        </ul>
                    </div>
                    <div class="col-md-9 col-xs-9 files">
                        <div id="remove-file-box" class="popup form-inline hidden">
                            <div class="align-center">
                                Are you sure you want to delete the file?
                            </div>

                            <div class="align-center move-down">
                                <button id="remove-file-ok" class="btn btn-primary">Ok</button>
                                <button id="remove-file-close" class="btn btn-default">Cancel</button>
                            </div>
                        </div>

                        <div id="rename-file-box" class="popup form-inline hidden">
                            <div class="form-group">
                                <label for="file-name" class="label-control">File Name:</label>
                                <input placeholder="File Name" id="rename-file-name" class="form-control"/>
                            </div>

                            <div class="align-center move-down">
                                <button id="rename-file-ok" class="btn btn-primary">Ok</button>
                                <button id="rename-file-close" class="btn btn-default">Close</button>
                            </div>
                        </div>

                        <ul id="fb_file_search_options" class="list-inline hidden"></ul>
                        <div>
                            <form id="file_browser_upload" class="form-inline hidden">
                                <div class="form-group">
                                    <input id="upload_file" name="file" type="file" class="form-control" required/>
                                </div>
                                <div id="upload_file_parameters" class="form-group">
                                </div>
                                <div class="form-group">
                                    <button id="upload_file_to_Server" type="button" class="btn btn-primary">Upload</button>
                                    <button id="cancel_file_upload" type="button" class="btn btn-default">Cancel</button>
                                </div>
                            </form>
                            <div class="text-center hidden" id="upload_file_loading"><i class="fa fa-spinner fa-spin fa-2x"></i></div>
                        </div>
                        <div id="search-enabled" class="hidden">
                            <label>Search :</label>
                            <button id="current-directory-search"></button>
                            <button id="disk-search">This disk</button>
                        </div>
                        <table id="files-list" class="table hidden"></table>
                        <ul id="files-grid" class="list-unstyled hidden"></ul>
                    </div>
                </div>

            </div>
            <div class="modal-footer">
                <ul id="show-file-details" class="list-inline hidden"></ul>
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary hidden" id="fb-primary-btn"></button>
            </div>
        </div>
    </div>
</div>


<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.form/3.51/jquery.form.min.js" ></script>
<script src="http://code.jquery.com/ui/1.9.2/jquery-ui.js"></script>

<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.js"></script>
<script src="//cdn.tinymce.com/4/tinymce.js"></script>
<script src="app/build/js/bundle.js"></script>
<script src="js/editor.js"></script>


</body>
</html>