<?php
require("../../ini/db.php");
require("../session/functions.php");
ini_set("display_errors", "1");
if(!isLogged($db)){
header('Location: ../index.php');
}
    ?>

    <!DOCTYPE html>
    <html lang="en"><head>
            <meta charset="utf-8">
            <title>Finesource - TTS</title>
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <meta name="description" content="">
            <meta name="author" content="">

            <!-- Le styles -->
            <link href="../css/style.css" rel="stylesheet">
            <link href="../css/bootstrap.css" rel="stylesheet">

            <!-- ADDED -->
            <link rel="stylesheet" href="style.css" />

            <link rel="stylesheet" href="../css/jquery-ui-1.8.16.custom.css" media="screen"  />
            <link rel="stylesheet" href="../css/fullcalendar.css" media="screen"  />
            <link rel="stylesheet" href="../css/chosen.css" media="screen"  />
            <link rel="stylesheet" href="../css/datepicker.css" >
            <link rel="stylesheet" href="../css/colorpicker.css">
            <link rel="stylesheet" href="../css/glisse.css?1.css">
            <link rel="stylesheet" href="../css/jquery.jgrowl.css">
            <link rel="stylesheet" href="../js/elfinder/css/elfinder.css" media="screen" />
            <link rel="stylesheet" href="../css/jquery.tagsinput.css" />
            <link rel="stylesheet" href="../css/demo_table.css" >
            <link rel="stylesheet" href="../js/export/css/TableTools.css" >
            <link rel="stylesheet" href="../css/validationEngine.jquery.css">
            <link rel="stylesheet" href="../css/jquery.stepy.css" />
            <link rel="stylesheet" href="../file-upload/bootstrap-fileupload.css" />
            <link rel="stylesheet" href="../css/color/black.css" />
            <link rel="stylesheet" href="../css/xcharts.min.css"/>
            <link rel="stylesheet" href="../css/jquery.easy-pie-chart.css"/>

            <link rel="stylesheet" href="../css/icon/font-awesome.css">    <link rel="stylesheet" href="../css/bootstrap-responsive.css">


        </head>

        <body>

            <style>
            </style>

            <!--Header Start-->
            <div class="header" >

                <!--Button User Start--> 
                <?php
                $params_logged_user = array($_SESSION['id_user']);

                $stmt = $db->prepare("SELECT name, last_name FROM zero.user_info WHERE id_user = ? LIMIT 1");
                $stmt->execute($params_logged_user);
                $results_logged_user = $stmt->fetchAll(PDO::FETCH_BOTH);

                $LoggedUser = $results_logged_user[0]["name"] . " " . $results_logged_user[0]["last_name"];
                ?> 
                <div class="btn-group pull-right" >
                    <a class="btn btn-profile dropdown-toggle" id="button-profile" data-toggle="dropdown" href="#">
                        <span class="name-user"><strong>Welcome</strong>, <?= $LoggedUser ?></span> 
                        <span class="avatar"><img src="../images/users/icon_user.png" alt="" ></span> 
                        <span class="caret"></span>
                    </a>
                    <div class="dropdown-menu" id="prof_dropdown">
                        <div class="item_m"><span class="caret"></span></div>
                        <ul class="clear_ul" >
                            <li><a id="a-logoff" href="#"><i class="icon-off"></i> Sign Out</a></li>
                        </ul>
                    </div>
                </div>
                <!--Button User END-->  

                <!-- Notifications -->      
                <div class="pull-right">
                    <div class="notifications-head">


                    </div>
                </div>      
                <!-- Notifications END -->      


            </div>
            <!--Header END-->


            <!--SIDEBAR START-->
            <?php
            $params_sidebar = array($_SESSION['id_user']);

            $stmt = $db->prepare("SELECT id_menu_link, path, icon, label FROM zero.menu_links WHERE id_user = ? AND visible = 1");
            $stmt->execute($params_sidebar);
            $results_sidebar = $stmt->fetchAll(PDO::FETCH_BOTH);

            $sidebar_html = "";
            for ($i = 0; $i < count($results_sidebar); $i++) {
                if ($i == 0) {
                    $sidebar_active = "active";
                    $sidebar_active_id = $results_sidebar[$i]['id_menu_link'];
                } else {
                    $sidebar_active = "";
                }
                $sidebar_html .= "<li><a menuid='" . $results_sidebar[$i]['id_menu_link'] . "' class='sidebar-nav $sidebar_active' href='#'><i pagetoload='" . $results_sidebar[$i]['path'] . "' class='" . $results_sidebar[$i]['icon'] . " sidebar-page-loader'></i><span pagetoload='" . $results_sidebar[$i]['path'] . "' class='sidebar-page-loader'>" . $results_sidebar[$i]['label'] . "</span></a></li>";
            }
            print "<div id='sidebar'><ul class='menu-sidebar'>$sidebar_html</ul></div>";
            ?>
            <!--SIDEBAR END-->

            <!--Content Start-->
            <div id="content">


                <!--SpeedBar Start-->
                <div class="speedbar">
                    <div class="speedbar-content">

                        <ul class="menu-drop">

                            <li><a href="#"><i class="icon-chevron-down "></i></a>

                                <ul class='ul-dropdown'>
                                    <div class="dropdown-scroll">
                                    </div>  
                                </ul>

                            </li>

                        </ul>


                        <div class='span' style='width:400px; margin-top:12px;'> 
                            <b style='color: #F2A500'>Campaign: </b><span style='margin:0px 6px 0px 6px; color:#FFFFFF' class='active-campaign'></span><b style='color: #F2A500; margin-left:12px'> Status: </b><span style='margin:0px 6px 0px 6px;' class='status-campaign'></span> 
                            <div class='clear'></div> 
                        </div>

                        <!--SPEEDBAR LINKS-->     
                        <?php
                        $params_speedbar = array($sidebar_active_id);

                        $stmt = $db->prepare("SELECT path, label FROM zero.menu_sub_links WHERE id_menu_link = ? AND visible = 1");
                        $stmt->execute($params_speedbar);
                        $results_speedbar = $stmt->fetchAll(PDO::FETCH_BOTH);

                        $speedbar_html = "";
                        for ($i = 0; $i < count($results_speedbar); $i++) {
                            if ($i == 0) {
                                $speedbar_active = "act_link";
                            } else {
                                $speedbar_active = "";
                            }
                            $speedbar_html .= "<li><a class='speedbar-nav " . $speedbar_active . "' href='" . $results_speedbar[$i]['path'] . "'>" . $results_speedbar[$i]['label'] . "</a></li>";
                        }
                        print "<ul class='menu-speedbar inner-speedbar'>$speedbar_html</ul>";
                        ?>
                        <!--SPEEDBAR LINKS END-->



                    </div>
                </div>


                <!--CONTENT MAIN START-->
                <div class="content inner-content" style='min-height:850px'>


                    <div id="graphs"></div>
                    <div id="kant"></div>





                </div>
                <!--CONTENT MAIN END-->

            </div>
            <!--Content END-->

            <!-- Le javascript
            ================================================== -->
            <!-- Placed at the end of the document so the pages load faster -->
            <script src="../js/jquery.min.js"></script>


            <script src="../js/bootstrap/js/bootstrap.js"></script>
            <script src="../js/bootstrap-datepicker.js"></script>
            <script src="../js/bootstrap-colorpicker.js"></script>
            <script src="../js/google-code-prettify/prettify.js"></script>

            <script src="../js/jquery.flot.min.js"></script>
            <script src="../js/jquery.flot.pie.min.js"></script>
            <script src="../js/jquery.flot.orderBars.js"></script>
            <script src="../js/jquery.flot.resize.js"></script>
            <script src="../js/graphtable.js"></script>
            <script src="../js/fullcalendar.min.js"></script>
            <script src="../js/chosen.jquery.min.js"></script>
            <script src="../js/autoresize.jquery.min.js"></script>
            <script src="../js/jquery.tagsinput.min.js"></script>
            <script src="../js/jquery.autotab.js"></script>
            <script src="../js/elfinder/js/elfinder.min.js" charset="utf-8"></script>
            <script src="../js/tiny_mce/tiny_mce.js"></script>
            <script src="../js/validation/languages/jquery.validationEngine-en.js" charset="utf-8"></script>
            <script src="../js/validation/jquery.validationEngine.js" charset="utf-8"></script>
            <script src="../js/jquery.jgrowl_minimized.js"></script>
            <script src="../js/jquery.dataTables.min.js"></script>
            <script src="../js/export/ZeroClipboard/ZeroClipboard.js"></script>
            <script src="../js/export/js/TableTools.js"></script>
            <script src="../js/jquery.mousewheel.js"></script>
            <script src="../js/jquery.jscrollpane.min.js"></script>
            <script src="../js/jquery.stepy.min.js"></script>
            <script src="../js/jquery.validate.min.js"></script>
            <script src="../js/raphael.2.1.0.min.js"></script>
            <script src="../js/justgage.1.0.1.min.js"></script>
            <script src="../js/glisse.js"></script>
            <script src="../js/styleswitcher.js"></script>

            <script src="../js/application.js"></script>

            <script src="../file-upload/bootstrap-fileupload.js"></script>
            <script src="../js/functions/warnings.js"></script>


            <script type="text/javascript" src="../js/jqueryui/js/jquery-ui-1.10.0.custom.min.js"></script>



            <!-- ///////////////////////////////////////////////////////////////////// -->
            <script src="../js/slimscroll/jquery.slimscroll.js"></script>

            <script src="../session/functions.js"></script>
            <script src="../notifications/functions.js"></script>
            <!-- Marco -->
            <script src="../js/graphs/d3.min.js"></script>
            <script src="../js/graphs/jquery.knob.js"></script>
            <script src="../js/graphs/jquery.knob.modified.min.js"></script>
            <script src="../js/graphs/justgage.1.0.1.min.js"></script>
            <script src="../js/graphs/moment.min.js"></script>
            <script src="../js/graphs/xcharts.min.js"></script>
            <script src="../js/graphsapi/api.js"></script>
            <script src="../js/graphsapi/graphcs.js"></script>
            <script src="../intra_realtime/scripts.js" ></script>
            <script src="../mod_main/script.js"></script>
            <!----->

            <script>
                var User = <?= $_SESSION['id_user']; ?>;

            </script>    
            <style>
                .border-test { border: 1px solid black; }
            </style>
        </body>
    </html>