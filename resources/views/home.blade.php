@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">Dashboard</div>
                <div class="panel-body">
                    <div class="text-center" style="margin-top: 10px;">
                        <button type="button" class="btn btn-primary btn-lg" 
                        data-disk-browser="true">
                            Launch demo modal
                        </button>

                        <div style="margin:0 auto; width: 70%;margin-top: 50px;">
                            <textarea id="tinyMCE"></textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@push('styles')
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.4.0/css/font-awesome.min.css" rel='stylesheet' type='text/css'>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" rel="stylesheet">
    <link href="/app/build/diskbrowser/css/disk-browser.css" rel="stylesheet">
@endpush
@push('body_scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.js"></script>
    <script src="/app/build/diskbrowser/js/disk-browser.js"></script>
    <script src="js/disk_browser.js"></script>
    <script src="//cdn.tinymce.com/4/tinymce.min.js"></script>
    <script type="text/javascript" src="js/tinymce_editor.js"></script>

@endpush
@endsection
