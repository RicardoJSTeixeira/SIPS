<?php
require("database/db_connect.php");
require("session/functions.php");
SessionStart();
if (count($_SESSION) > 0) {
    $login_flag = 1;
} else {
    $login_flag = 0;
}
//print_r($_SESSION);
?>
<!DOCTYPE html>
<html lang="en"  class="body-error"><head>
        <meta charset="utf-8">
        <title>Finesource - TTS</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="">
        <meta name="author" content="">

        <!-- Le styles -->







        <link rel="alternate stylesheet" type="text/css" media="screen" title="green-theme" href="css/color/green.css" />
        <link rel="alternate stylesheet" type="text/css" media="screen" title="red-theme" href="css/color/red.css" />
        <link rel="alternate stylesheet" type="text/css" media="screen" title="black-theme" href="css/color/black.css" />
        <link rel="alternate stylesheet" type="text/css" media="screen" title="orange-theme" href="css/color/orange.css" />
        <link rel="alternate stylesheet" type="text/css" media="screen" title="purple-theme" href="css/color/purple.css" />
        <link rel="alternate stylesheet" type="text/css" media="screen" title="silver-theme" href="css/color/silver.css" />
        <link rel="alternate stylesheet" type="text/css" media="screen" title="metro-theme" href="css/color/metro.css" />

        <!-- Le HTML5 shim, for IE6-8 support of HTML5 elements -->
        <!--[if lt IE 9]>
          <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
        <![endif]-->
        <!--[if lte IE 8]><script type="text/javascript" src="/js/excanvas.min.js"></script><![endif]-->

        <!-- Le fav and touch icons -->
        <link rel="shortcut icon" href="images/icons/favicon.ico">


        <!-- -->
        <link href="mod_login/login.css" rel="stylesheet">

        <script src="security/validation.js"></script>
        <script src="session/functions.js"></script>
        <script src="js/functions/warnings.js"></script>
        <script src="js/jquery/jquery.min.js"></script>

        <script src="security/sha512.js"></script>

        <script src="js/bootstrap/js/bootstrap.min.js"></script>
        <link rel="stylesheet" href="js/bootstrap/css/bootstrap.sweetdreams.css"> 
        <link rel="stylesheet" href="js/bootstrap/css/bootstrap-responsive.min.css">

        <link rel="stylesheet" href="css/font-awesome/font-awesome.css">
        <link rel="stylesheet" href="css/animate/animate.min.css">




    </head>

    <body>



        <div id="container_demo" >
            <div id="wrapper" >
                <div id="login" class="position animated">





                    <form autocomplete="off" class="form-login"> 
                        <div class="content-login">
                            <!-- <a href="#" class='logo' ></a> -->
                            <img style='margin-top:20px' src="images/users/fs_white.jpg">

                            <div class="inputs">
                                <i class="icon-envelope-alt first-icon animated input-login-email-icon"></i><input id="input-login-email" name="" type="text" class="first-input input-class" placeholder="email" />
                                <div class="clear"></div>
                                <i class="icon-key animated input-login-password-icon"></i><input id="input-login-password" name="" type="password" class="last-input input-class" placeholder="password" />
                            </div>

                            <!--    <div class="remember">
                                    <input type="checkbox" id="c2" name="cc" checked="checked" />
                                            <label for="c2"><span></span> Remember Me</label>
                                </div>
                                <div class="link"><a href="#">Forgot Password?</a></div> --> 
                            <div class="clear"></div>
                            <div class="button-login"><input id="input-login" type="button" class="btn btn-large custom-button" value="Sign In"></div>
                        </div>

                        <div class="footer-login">
                            <!--      <div class="pull-left ">Don't have an account?</div> 
                                  <div class="pull-right"><a class="animate-login-form" href="#">Create Account</a></div> -->
                            <div class="clear"></div>
                        </div>

                    </form>
                    <div id="alert-box-login" class="info-message"></div>
                    <!--   <div class="info-message">
                           <div class="alert alert-info">        
                           If you need a new profile, click "Creative Account"
                           </div>
                       </div> -->

                </div>

                <div id="register" class="position animated hide" >
                    <form autocomplete="off" class="form-login"> 
                        <div class="content-login" >
                            <!-- <a href="#" class="logo"></a> -->

                            <img style='margin-top:20px' src="images/users/fs_white.jpg">

                            <div class="inputs">
                             <!--    <i class="icon-user first-icon"></i><input id="input-register-username" name="" type="text" class="first-input" placeholder="username" /> -->

                                <i style="margin-top:10px" class="icon-user animated input-register-first-name-icon"></i><input  id="input-register-first-name" name="" type="text" class="first-input input-class" placeholder="first name" />
                                <div class="clear"></div>
                                <i class="icon-user animated input-register-last-name-icon"></i><input id="input-register-last-name" name="" type="text" class="last-input input-class" placeholder="last name" />



                                <i style="margin-top:15px" class="icon-envelope-alt first-icon animated input-register-email-icon"></i><input style="margin-top:6px" id="input-register-email" name="" type="text" class="first-input input-class" placeholder="email" /> 
                                <div class="clear"></div>
                                <i class="icon-key animated input-register-password-icon"></i><input id="input-register-password" name="" type="password" class="last-input input-class" placeholder="password" />





                            </div>

                            <div class="button-login"><input id="input-register-account" type="button" class="btn btn-large custom-button" value="Create Account"></div>

<!--     <div class="or"><span>or</span></div>
<div class="buttons-soc">
  <a href="#"><span><i class="icon-twitter"></i> Connect with Twitter</span></a>
  <a href="#" class="facebook"><span><i class="icon-facebook"></i> Connect with Facebook</span></a>
  <div class="clear"></div>
</div> -->

                        </div> 

                        <div class="footer-login">
                            <div class="pull-left ">Want login?</div>
                            <div class="pull-right"><a class="animate-register-form" href="#" >Sign In</a></div>
                            <div class="clear"></div>
                        </div>
                    </form>
                    <div id="alert-box-register" class="info-message"></div>


                </div>

            </div>
        </div>  



        <script>
            // Login Form and Register Form Animation triggers   
            $(".animate-login-form").click(function() {
                AnimateLogin();
            });
            $(".animate-register-form").click(function() {
                AnimateRegister();
            });

            // Close all alerts when you edit or focus an input
            $(".input-class").bind("input focus click", function() {
                $(".alert").alert("close");
            });

            // Animation for the login box
            function AnimateLogin() {
                // Animation classes
                $("#login").removeClass("fadeInRight").addClass("bounceOutLeft");
                $("#register").removeClass("bounceOutLeft hide").addClass("fadeInRight");
                // Clear all input values
                $(".input-class").val("");
                // Close all alerts
                $(".alert").alert("close");
                // Assign a focus
                $("#input-register-email").focus();
            }

            // Animation for the register box
            function AnimateRegister() {
                // Animation classes
                $("#register").removeClass("fadeInRight").addClass("bounceOutLeft");
                $("#login").removeClass("bounceOutLeft").addClass("fadeInRight");
                // Clear all input values
                $(".input-class").val("");
                // Close all alerts
                $(".alert").alert("close");
                // Assign a focus
                if ($("#input-login-email").val() === "") {
                    $("#input-login-email").focus();
                } else {
                    $("#input-login-password").focus();
                }
                ;
            }

            // Animation for input icons
            function AnimateIcon(Element) {
                Element.addClass("wobble");
                window.setTimeout(function() {
                    Element.removeClass("wobble");
                }, 1500);
            }

            //Email Register Popup
            $("#input-register-email").blur(function() {
                $(this).popover("hide");
            });
            $("#input-register-email").bind("input focus", function() {
                if ($(this).val().length !== 0) {
                    var result = validateInput($(this).val(), "email");
                    var label = result.label;
                    $(this).popover("destroy").popover({
                        animation: false,
                        html: true,
                        title: "<b>Your E-mail</b>",
                        content: "Your e-mail will be used for password recovery and comunication between the aplication and you, so we ask you to insert a valid e-mail that you usually access. <br><br> Your e-mail seems " + label,
                        trigger: "manual"
                    }).popover("show");
                }
            });

            //Password Register Popup
            $("#input-register-password").blur(function() {
                $(this).popover("hide");
            });
            $("#input-register-password").bind("input focus", function() {
                if ($(this).val().length !== 0) {
                    var result = validateInput($(this).val(), "password");
                    var label = result.label;
                    $(this).popover("destroy").popover({
                        animation: false,
                        html: true,
                        title: "<b>Your Password</b>",
                        content: "Try to choose a strong password for security reasons. <br><br> Strong passwords use lower and uppercase letters, numbers or symbols like [!,@,#,$,%,^,&,*,?,_,~]. <br><br> Your password should have between 4 and 16 characters and have no spaces. <br><br> Your password is " + label,
                        trigger: "manual"
                    }).popover("show");
                }
            });

            // Register Account
            $("#input-register-account").click(function() {
                var Email = $("#input-register-email"),
                        Password = $("#input-register-password"),
                        FirstName = $("#input-register-first-name").val(),
                        LastName = $("#input-register-last-name").val(),
                        EmailResult = validateInput(Email.val(), 'email'),
                        PasswordResult = validateInput(Password.val(), 'password');

                if (EmailResult.validation === 0) {
                    makeAlert("#alert-box-register", "Account Info is Wrong", "Your E-mail is not valid!", 3, 1, 1);
                    AnimateIcon($(".input-register-email-icon"));
                    return false;
                }
                if (PasswordResult.validation === 0) {
                    makeAlert("#alert-box-register", "Account Info is Wrong", "Your Password is not valid!", 3, 1, 1);
                    AnimateIcon($(".input-register-password-icon"));
                    return false;
                }

                if (FirstName.length === 0) {
                    makeAlert("#alert-box-register", "Account Info is Wrong", "Please write your name!", 3, 1, 1);
                    AnimateIcon($(".input-register-first-name-icon"));
                    return false;
                }
                if (LastName.length === 0) {
                    makeAlert("#alert-box-register", "Account Info is Wrong", "Please write your last name!", 3, 1, 1);
                    AnimateIcon($(".input-register-last-name-icon"));
                    return false;
                }

                $.ajax({
                    type: "POST",
                    url: "mod_login/requests.php",
                    dataType: "JSON",
                    data: {zero: "InsertNewUser",
                        email: Email.val(),
                        firstname: FirstName,
                        lastname: LastName,
                        password: hex_sha512(Password.val())
                    },
                    success: function(data) {
                        // console.log(data.result[1]);

                        if (!data.result[0]) {
                            makeAlert("#alert-box-register", "Email already registered", "This email is already taken, please choose another one!", 3, 1, 1);
                            AnimateIcon($(".input-register-email-icon"));
                            return false;
                        }
                        else {
                            AnimateRegister();
                        }
                    }
                });

            });




            // Login
            var login_flag = <?php echo $login_flag; ?>;
            $("#alert-box-force-logout").live("click", function() {
                LogOut('<?php
if ($login_flag) {
    echo $_SESSION['id_user'];
} else {
    echo 0;
}
?>');
                login_flag = 0;
                $(".alert").alert("close");
            });
            $("#input-login").click(function() {
                $(".alert").alert("close");
                var Email = $("#input-login-email"),
                        Password = $("#input-login-password"),
                        EmailResult = validateInput(Email.val(), "email"),
                        PasswordResult = validateInput(Password.val(), "password");

                if (login_flag) {
                    makeAlert("#alert-box-login", "You are already Logged In!", "Click <a href='#' id='alert-box-force-logout'>here</a> to terminate the current session or <a href='mod_main'>here</a> to view te welcome page.", 3, 1, 1);
                    return false;
                }
                if (EmailResult.validation === 0) {
                    makeAlert("#alert-box-login", "Email seems wrong :(", "Please insert a valid email!", 3, 1, 1);
                    AnimateIcon($(".input-login-email-icon"));
                    return false;
                }
                if (PasswordResult.validation === 0) {
                    makeAlert("#alert-box-login", "Password seems wrong :(", "Please insert a valid password!", 3, 1, 1);
                    AnimateIcon($(".input-login-password-icon"));
                    return false;
                }

                $.ajax({
                    type: "POST",
                    url: "session/requests.php",
                    dataType: "JSON",
                    data: {zero: "Login",
                        email: Email.val(),
                        password: hex_sha512(Password.val()),
                    },
                    success: function(data1) {
                        // console.log(data1.result);
                        if (data1.result[0] === false) {
                            makeAlert("#alert-box-login", "Login Failed :(", "Please correct your credencials!", 3, 1, 1);
                        } else {
                            window.location = "mod_main";
                        }
                    }
                });
            });




        </script>  

    </body>
</html>

