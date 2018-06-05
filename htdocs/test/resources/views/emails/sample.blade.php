<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>PokerZania</title>
    <!-- Bootstrap Core CSS -->
    <link href="bootstrap/dist/css/bootstrap.css" rel="stylesheet">
    <!-- animation CSS -->
    <link href="css/animate.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="css/style.css" rel="stylesheet">
    <!-- color CSS -->
    <link href="css/colors/blue.css" id="theme"  rel="stylesheet">
    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->


</head>
<body background="body.jpg">
<!-- Preloader -->


<section id="wrapper" class="login-register">
    <div class="login-box">
        <div class="white-box">

            <form class="form-horizontal form-material" id="loginform" action="{{ url('/login') }}" method="POST">
                {!! csrf_field() !!}
               <h3 class="box-title m-b-20">Sign In</h3>
                <div class="form-group">
                    <div class="col-xs-12">
                        <input class="form-control" type="text" required="" placeholder="Email" name="email" id="email">
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-xs-12">

                        <input class="form-control" id="password"  name="password" type="password" required="" placeholder="Password">
                    </div>
                </div>


                <div class="form-group text-center m-t-20">
                    <div class="col-xs-12">
                        <button class="btn  btn-rounded btn-block btn-info" type="submit">Log In</button>
                    </div>
                </div>
                <div class="row">

                </div>
            </form>
            <form class="form-horizontal form-material" id="signupform" action="{{ url('/signup') }}" method="POST">
                {!! csrf_field() !!}
                <div class="form-group m-b-0">
                    <div class="col-sm-12 text-center">
                        <p>Don't have an account?
                        <button class="btn btn-outline btn-default btn-xs  btn-rounded btn-success " type="submit">Sign Up</button>
                        </p>
                    </div>
                </div>
            </form>
            <form class="form-horizontal" id="recoverform" action="index.html">
                {!! csrf_field() !!}
                <div class="form-group ">
                    <div class="col-xs-12">
                        <h3>Recover Password</h3>
                        <p class="text-muted">Enter your Email and instructions will be sent to you! </p>
                    </div>
                </div>
                <div class="form-group ">
                    <div class="col-xs-12">
                        <input class="form-control" type="text" required="" placeholder="Email">
                    </div>
                </div>
                <div class="form-group text-center m-t-20">
                    <div class="col-xs-12">
                        <button class="btn btn-primary btn-lg btn-block text-uppercase waves-effect waves-light" type="submit">Reset</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</section>
<!-- jQuery -->
<script src="../plugins/bower_components/jquery/dist/jquery.min.js"></script>
<!-- Bootstrap Core JavaScript -->
<script src="bootstrap/dist/js/bootstrap.min.js"></script>
<!-- Menu Plugin JavaScript -->
<script src="../plugins/bower_components/sidebar-nav/dist/sidebar-nav.min.js"></script>

<!--slimscroll JavaScript -->
<script src="js/jquery.slimscroll.js"></script>
<!--Wave Effects -->
<script src="js/waves.js"></script>
<!-- Custom Theme JavaScript -->
<script src="js/custom.min.js"></script>
<!--Style Switcher -->
<script src="../plugins/bower_components/styleswitcher/jQuery.style.switcher.js"></script>
</body>
</html>