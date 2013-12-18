<?php
require("../ini/db.php");
require("session/functions.php");
SessionStart();
$login_flag = (count($_SESSION)) ? 1 : 0;

if(isLogged($db)){
header('Location: mod_main/index.php');
}
//print_r($_SESSION);
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title>Finesource - TTS - Login</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="">
        <meta name="author" content="">

        <!-- Le styles -->
        <link href="mod_login/login.css" rel="stylesheet">

        <link href="/bootstrap/css/bootstrap.css" rel="stylesheet">

        <link rel="stylesheet" href="/bootstrap/icon/font-awesome.css">    
        <link rel="stylesheet" href="/bootstrap/css/bootstrap-responsive.css">

        <link rel="shortcut icon" href="/images/icons/favicon.ico">
        <link rel="stylesheet" href="css/animate/animate.min.css">

    </head>

    <body>



        <div id="container_demo" >
            <div id="wrapper" >
                <div id="login" class="position animated">





                    <form autocomplete="on" class="form-login"  id="input-login"> 
                        <div class="content-login">
                            <img style='margin-top:20px' src="images/users/fs_white.jpg">

                            <div class="inputs">
                                <i class="icon-envelope-alt first-icon animated input-login-email-icon"></i><input id="input-login-email" name="" type="text" class="first-input input-class" placeholder="email" />
                                <div class="clear"></div>
                                <i class="icon-key animated input-login-password-icon"></i><input id="input-login-password" name="" type="password" class="last-input input-class" placeholder="password" />
                            </div>

                            <div class="clear"></div>
                            <div class="button-login"><button class="btn btn-large custom-button">Sign In</button></div>
                        </div>

                        <div class="footer-login">
                                  <div class="pull-left ">Don't have an account?</div> 
                                  <div class="pull-right"><a class="animate-login-form" href="#">Create Account</a></div> 
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

                            <img style='margin-top:20px' src="images/users/fs_white.jpg">

                            <div class="inputs">

                                <i style="margin-top:10px" class="icon-user animated input-register-first-name-icon"></i><input  id="input-register-first-name" name="" type="text" class="first-input input-class" placeholder="First name" />
                                <div class="clear"></div>
                                <i class="icon-user animated input-register-last-name-icon"></i><input id="input-register-last-name" name="" type="text" class="last-input input-class" placeholder="Last name" />



                                <i style="margin-top:15px" class="icon-envelope-alt first-icon animated input-register-email-icon"></i><input style="margin-top:6px" id="input-register-email" name="" type="text" class="first-input input-class" placeholder="Email" /> 
                                <div class="clear"></div>
                                <i class="icon-key animated input-register-password-icon"></i><input id="input-register-password" name="" type="password" class="last-input input-class" placeholder="Password" />

                            </div>

                            <div class="button-login"><input id="input-register-account" type="button" class="btn btn-large custom-button" value="Create Account"></div>


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

        <script src="/jquery/jquery-1.9.1.js"></script>
        <script type="text/javascript" src="/bootstrap/js/bootstrap.min.js"></script>
        <script src="security/validation.js"></script>
        <script src="session/functions.js"></script>
        <script src="js/functions/warnings.js"></script>

        <script src="security/sha512.js"></script>


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

                $.post("mod_login/requests.php", {
                    zero: "InsertNewUser",
                    email: Email.val(),
                    firstname: FirstName,
                    lastname: LastName,
                    password: hex_sha512(Password.val())
                }, function(data) {
                    // console.log(data.result[1]);

                    if (!data.result[0]) {
                        makeAlert("#alert-box-register", "Email already registered", "This email is already taken, please choose another one!", 3, 1, 1);
                        AnimateIcon($(".input-register-email-icon"));
                        return false;
                    }
                    else {
                        AnimateRegister();
                    }
                },
                        "json");

            });

            // Login
            var login_flag = <?= $login_flag; ?>;
            $(document).on("click", "#alert-box-force-logout", function() {
                LogOut('<?= ($login_flag) ? $_SESSION['id_user'] : 0 ?>');
                login_flag = 0;
                $(".alert").alert("close");
            });

            $("#input-login").submit(function(e) {
                e.preventDefault();
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

                $.post("session/requests.php", {zero: "Login",
                    email: Email.val(),
                    password: hex_sha512(Password.val())
                },
                function(data1) {
                    // console.log(data1.result);
                    if (data1.result[0] === false) {
                        makeAlert("#alert-box-login", "Login Failed :(", "Please correct your credencials!", 3, 1, 1);
                    } else {
                        window.location = "mod_main";
                    }
                }
                , "json");
            });

        </script>  

    </body>
</html>

