<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login</title>

    <link href='https://fonts.googleapis.com/css?family=Open+Sans:300italic,300' rel='stylesheet' type='text/css'>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" rel="stylesheet"/>

    <style>

        body {
            font-family: 'Open Sans', sans-serif;
        }
        .form-control {
            border-radius: 0;
        }

        .bg-colour {
            background: #f0f0f0;
        }

        .login {
            margin: 0 auto;
            width: 450px;
            height: 220px;
            text-align: center;
            position: absolute;
            top: calc(50vh - 200px);
            left: calc(50vw - 225px);
        }

        .login p {
            font-size: 0.9em;
            font-style: italic;
            margin-top: 10px;
            opacity: 0.5;
        }

        .login img {
            margin-bottom: 10px;
        }

        .login form {
            text-align: left;
            width: 100%;
            height: 100%;
            padding: 30px 20px 20px 20px;
            position:relative;
            background: #fff;
            border: 1px solid #ccc;
            border-radius: 3px;
        }

        .form-group {
            margin-bottom: 50px
        }


    </style>

</head>
<body class="bg-colour">

    @if (count($errors) > 0)
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="container-fluid">

        <div class="login">

            <img src="//dffhdzon8s5n6.cloudfront.net/images/site/logo.png"
                 alt="Energy Aspects logo" width="200" height="75" id="login-logo">

            <form method="POST" action="/login">

                {{ csrf_field() }}

                <div class="form-group">
                    <label class="col-md-4 control-label">E-mail address</label>
                    <div class="col-md-8">
                        <input id="email" type="email" id="email" class="form-control" name="email" value="" required />
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-4 control-label">Password</label>
                    <div class="col-md-8">
                        <input  id="password" type="password" class="form-control" name="password" required />
                    </div>
                </div>

                <div class="form-group">
                    <div class="col-md-6 col-md-offset-4">
                        <div class="checkbox">
                            <label>
                                <input type="checkbox" name="remember"> Remember Me
                            </label>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <div class="col-md-6 col-md-offset-4">
                        <button type="submit" style="margin-right: 15px;">
                            Login
                        </button>
                    </div>
                </div>

            </form>

            <p>This system may only be accessed by Energy Aspects employees.</p>

        </div>

    </div>

    <script type="text/javascript">
        document.getElementById("email").focus();
    </script>
</body>
</html>