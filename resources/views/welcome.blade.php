<!DOCTYPE html>
<html>
<head lang="en">
    <meta charset="UTF-8">
    <title>File browser</title>
    <!-- Fonts -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.4.0/css/font-awesome.min.css" rel='stylesheet' type='text/css'>

    <!-- Styles -->
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="http://code.jquery.com/ui/1.9.2/themes/base/jquery-ui.css">
    <link href="/app/build/diskbrowser/css/disk-browser.css" rel="stylesheet">
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

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>
<script src="http://code.jquery.com/ui/1.9.2/jquery-ui.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.form/3.51/jquery.form.min.js" ></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.js"></script>

<script src="/app/build/diskbrowser/js/disk-browser.js"></script>
<script src="js/disk_browser.js"></script>

<script src="//cdn.tinymce.com/4/tinymce.js"></script>

</body>
</html>