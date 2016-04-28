<!DOCTYPE html>
<html>
    <head>
        <title>Screen Live</title>
        <link href="https://fonts.googleapis.com/css?family=Lato:100" rel="stylesheet" type="text/css">
        <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" rel="stylesheet" type="text/css">

        <style>
            .scheduled {
                background: #ccc;
            }

            .sent {
                background: #666;
                color:#fff;
            }

            .opened {
                background-color: #CBE0A9;
            }

            .downloaded {
                background-color: #E6FFC3;
            }

            .table.publications>tbody>tr>td, .table.publications>thead>tr>th {
                border-bottom: 1px solid black;
                border-top: none;
            }

            .header {
                width: 100%;
                height: 50px;
                vertical-align: middle;
                display: inline-block;
                margin: 0;
                border-bottom: 1px solid #999;
                background-color: #006699;
                color: #fff;
                padding: 3px 10px;
                font-size: 200%;
            }

            .wrapper {
                padding: 15px;
                height: 90vh;

                background: white;
                overflow: auto;
            }

            .wrapper.left {
                padding-left: 30px;
            }

            .section {
                border: 1px solid black;
                margin-bottom: 10px;
            }

            .activities td.status{
                width: 15%;
            }

            .activities td.title{
                width: 60%;
            }

            #email-server-status {
                position:fixed;
                bottom: 25px;
                left: 20px;
            }

            .status-icon {
                display: inline-block;
                height: 15px;
                width: 15px;
                line-height: 15px;
                border-radius: 50%;
                background: darkgray;
            }

            .status-icon.success {
                background: green;
            }

            .status-icon.failed {
                background: red;
            }

            .status-icon.failing {
                background: orangered;
            }
        </style>
    </head>

    <body>
    <div class="row">
        <div class="col-md-6 wrapper left">
            <div class="section">
                <div class="header">
                    Recent Publications
                </div>
                <table class="table table-striped publications">
                    <thead>
                    <tr>
                        <th>&nbsp;</th>
                        <th>Scheduled</th>
                        <th>Sent</th>
                        <th>Opened e-mail(unique)</th>
                        <th>Downloads(unique)</th>
                    </tr>
                    </thead>
                    <tbody>
                    {{--{{ dd($emailData) }}--}}
                    @foreach($emailData as $email)

                        <tr>
                            <td>
                                <p>
                                    <a href='https://www.energyaspects.com/email-pdf-reports/edit/{{ $email['id'] }}?returnpage=recipients'>
                                        {{ $email['subject'] }}
                                    </a>
                                </p>
                            </td>
                            <td class="scheduled">
                                <p>
                                    {{ $email['scheduled'] }}
                                </p>
                            </td>
                            <td class="sent">
                                <p>
                                    {{ $email['sent'] }}
                                </p>
                            </td>
                            <td class="opened">
                                <p>
                                    @if($email['opened'] > 0)
                                        {{ $email['opened'] }}({{ $email['uniqueOpened'] }})
                                    @else
                                        -
                                    @endif
                                </p>
                            </td>
                            <td class="downloaded">
                                @foreach($email['downloads'] as $downloads)
                                    <p>
                                        @if($downloads['downloaded'] > 0)
                                            {{ $downloads['downloaded'] }}({{ $downloads['uniqueDownloaded'] }})
                                        @else
                                            -
                                        @endif
                                    </p>
                                @endforeach
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
                <div id="email-server-status">
                    <span class="status-icon">&nbsp;</span> Waiting for connection
                </div>
            </div>
        </div>
        <div class="col-md-6 wrapper">
            @foreach($ipData as $detail)
            <div class="section">
                <div class="header">
                    {{ $detail['name'] }}
                </div>
                <div>
                    {{ $detail['address'] }} ..
                </div>
                <table class="table activities">
                    <tbody>
                        @foreach( $detail['activities'] as $activity)
                        <tr>
                            <td class="status">
                                {{ $activity['status'] }}
                            </td>
                            <td class="title">
                                {{ $activity['title'] }}
                            </td>
                            <td>
                                {{ $activity['timestamp'] }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endforeach
        </div>
    </div>

    <script src="https://js.pusher.com/3.0/pusher.min.js"></script>
    <script src="js/pusher.js"></script>
    </body>
</html>
