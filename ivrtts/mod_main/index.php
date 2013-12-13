<?php
require("../database/db_connect.php");
require("../session/functions.php");
ini_set("display_errors", "1");
if (isLogged()) {
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
            <link rel="stylesheet" href="../js/jqplot/jquery.jqplot.css" />


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

            <link rel="alternate stylesheet" type="text/css" media="screen" title="green-theme" href="../css/color/green.css" />
            <link rel="alternate stylesheet" type="text/css" media="screen" title="red-theme" href="../css/color/red.css" />
            <link rel="alternate stylesheet" type="text/css" media="screen" title="black-theme" href="../css/color/black.css" />
            <link rel="alternate stylesheet" type="text/css" media="screen" title="orange-theme" href="../css/color/orange.css" />
            <link rel="alternate stylesheet" type="text/css" media="screen" title="purple-theme" href="../css/color/purple.css" />
            <link rel="alternate stylesheet" type="text/css" media="screen" title="silver-theme" href="../css/color/silver.css" />
            <link rel="alternate stylesheet" type="text/css" media="screen" title="metro-theme" href="../css/color/metro.css" />


            <link rel="shortcut icon" href="../images/icons/favicon.ico">

        </head>

        <body>


            <style>
            </style>

            <!--Header Start-->
            <div class="header" >


                <!--Button User Start--> 
                <?
                $params_logged_user = array($_SESSION['id_user']);
                $results_logged_user = $db->rawQuery("SELECT name, last_name FROM zero.user_info WHERE id_user = ? LIMIT 1", $params_logged_user);
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
            $results_sidebar = $db->rawQuery("SELECT id_menu_link, path, icon, label FROM zero.menu_links WHERE id_user = ? AND visible = 1", $params_sidebar);
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
            print "<div id='sidebar'>    
        <ul class='menu-sidebar'>
            $sidebar_html
        </ul>
    </div>    
    ";
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
                        $results_speedbar = $db->rawQuery("SELECT path, label FROM zero.menu_sub_links WHERE id_menu_link = ? AND visible = 1", $params_speedbar);
                        $speedbar_html = "";
                        for ($i = 0; $i < count($results_speedbar); $i++) {
                            if ($i == 0) {
                                $speedbar_active = "act_link";
                            } else {
                                $speedbar_active = "";
                            }
                            $speedbar_html .= "<li><a class='speedbar-nav " . $speedbar_active . "' href='" . $results_speedbar[$i]['path'] . "'>" . $results_speedbar[$i]['label'] . "</a></li>";
                        }
                        print "<ul class='menu-speedbar inner-speedbar'>
        $speedbar_html
    </ul>
    ";
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

            <script src="../js/jqplot/jquery.jqplot.js"></script>

            <script src="../js/jqplot/plugins/jqplot.barRenderer.min.js"></script>
            <script src="../js/jqplot/plugins/jqplot.categoryAxisRenderer.min.js"></script>
            <script src="../js/jqplot/plugins/jqplot.pointLabels.min.js"></script>

            <script src="../js/jqplot/plugins/jqplot.pieRenderer.min.js"></script>
            <script src="../js/jqplot/plugins/jqplot.donutRenderer.min.js"></script>
            <script src="../js/jqplot/plugins/jqplot.canvasTextRenderer.min.js"></script>
            <script src="../js/jqplot/plugins/jqplot.canvasAxisLabelRenderer.min.js"></script>
            <script src="../js/jqplot/plugins/jqplot.dateAxisRenderer.js"></script>


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
            
            <!----->

            <script>
                // INI





                var CurrentCampaign;

                var CurrentCampaignID;

                

                


                function PopulateDropDownAndCurrentCampaign(Reload)
                {

                    $.ajax({
                        type: "POST",
                        dataType: "JSON",
                        url: "requests.php",
                        data: {zero: "GetActiveCampaignsDropDownList"},
                        success: function(data) {
                            //console.log(data);
                            $(".dropdown-scroll").empty();

                            if (data === null)
                            {
                                if (typeof CurrentCampaign !== 'undefined') {
                                    $(".dropdown-scroll").append("<li><a href='#'>There are no Campaigns created.</li>");
                                    $("#kant").html("<br><center><b>No Campaigns.</b></a><img style='margin-left:8px' src='../images/users/icon_emotion_sad_32.png'></center>");
                                    $('.dropdown-scroll').slimScroll({
                                        position: 'right',
                                        height: '30px',
                                        railVisible: false,
                                        wheelStep: 8
                                    });
                                    $(".ul-dropdown").css("width", "210px");
                                    //CurrentCampaign = undefined; 
                                    //$(".current-campaign").html(""); 
                                }
                            }
                            else
                            {
                                $.each(data.camp_list, function(index, value) {
                                    if (index === 0 && !Reload) {
                                        CurrentCampaignID = value['campaign_id'];
                                        CurrentCampaign = value['campaign_name'];
                                        $(".active-campaign").html(value['campaign_name']);
                                        if (value['active'] == 'Y') {
                                            $(".status-campaign").html("<span style='background-color:#468847' class='label label-success'>Running</span>")
                                        } else {
                                            $(".status-campaign").html("<span style='background-color:#b94a48' class='label label-important'>Stopped</span>")
                                        }
                                    } else {
                                        if (Reload && value['campaign_name'] == CurrentCampaign)
                                        {
                                            $(".active-campaign").html(value['campaign_name']);
                                            if (value['active'] == 'Y') {
                                                $(".status-campaign").html("<span style='background-color:#468847' class='label label-success'>Running</span>")
                                            } else {
                                                $(".status-campaign").html("<span style='background-color:#b94a48' class='label label-important'>Stopped</span>")
                                            }

                                        }


                                    }



                                    $(".dropdown-scroll").append("<li><a href='#' class='quick-choose-campaign' campaign-active='" + value['active'] + "' campaign='" + value['campaign_id'] + "'>" + value['campaign_name'] + "</a></li>");
                                });
                                $('.dropdown-scroll').slimScroll({
                                    position: 'right',
                                    height: '210px',
                                    railVisible: false,
                                    wheelStep: 8
                                });

                                if ($(".sidebar-page-loader[pagetoload='../intra_realtime/index.php']").parent().hasClass("active")) {
                                    GetRealtime();
                                }

                            }

                        }
                    });

                }

                $(".quick-choose-campaign").live("click", function() {



                    CurrentCampaign = $(this).html();
                    CurrentCampaignID = $(this).attr("campaign");

                    $(".active-campaign").html(CurrentCampaign);
                    if ($(this).attr("campaign-active") == 'Y') {
                        $(".status-campaign").html("<span style='background-color:#468847' class='label label-success'>Running</span>")
                    } else {
                        $(".status-campaign").html("<span style='background-color:#b94a48' class='label label-important'>Stopped</span>")
                    }


                    // GetRealtime();

                    $(".sidebar-page-loader[pagetoload='../intra_realtime/index.php']").click();


                });



               function GetRealtime()
                {

                   dashboard();
                  /* $.ajax({
                        type: "POST",
                        url: "../intra_realtime/requests.php",
                        dataType: "JSON",
                        data: {action: "GetRealtimeTotals", sent_campaign: CurrentCampaignID},
                        success: function(data00) {


                            $.ajax({
                                type: "POST",
                                url: "../intra_realtime/requests.php",
                                dataType: "JSON",
                                data: {action: "GetDatabaseTotals", sent_campaign: CurrentCampaignID},
                                success: function(data0) {
                                    $.ajax({
                                        type: "POST",
                                        dataType: "JSON",
                                        url: "../intra_realtime/requests.php",
                                        data: {action: "GetCampaignTotals", sent_campaign: CurrentCampaignID},
                                        success: function(data1)
                                        {
                                            $.ajax({
                                                type: "POST",
                                                url: "../intra_realtime/index.php",
                                                data: {
                                                    campaign: $(this).attr("campaign"),
                                                    totals1: data1.feitas,
                                                    totals2: data1.atendidas,
                                                    totals3: data1.naoatendidas,
                                                    totals4: data1.ouvidas,
                                                    totals5: data1.declinadas,
                                                    dbtotals1: data0.MSG001,
                                                    dbtotals2: data0.MSG002,
                                                    dbtotals3: data0.MSG003,
                                                    dbtotals4: data0.MSG004,
                                                    dbtotals5: data0.MSG005,
                                                    dbtotals6: data0.MSG006,
                                                    dbtotals7: data0.MSG007,
                                                    dbtotals_new: data0.NEW,
                                                    dbtotals_outros: data0.OUTROS,
                                                    rtouvidas: data00.ouvidas,
                                                    rtdeclinadas: data00.declinadas,
                                                    rtfeitas: data00.feitas
                                                },
                                                success: function(data2) {

                                                    $("#kant").html(data2);
                                                }
                                            });
                                        }
                                    });

                                }
                            });

                        }
                    });
                        */

                }



                $("#a-logoff").click(function() {
                    LogOut(<?php echo $_SESSION['id_user']; ?>);
                });

                $(".speedbar-nav").click(function() {
                    $.ajax({
                        type: "GET",
                        url: $(this).attr("href"),
                        success: function(data) {
                            $("#kant").html(data);
                        }
                    });
                    $(".speedbar-nav").removeClass("act_link");
                    $(this).addClass("act_link");
                    return false;
                });



                $(".sidebar-page-loader").click(function() {

                    if ($(this).attr("pagetoload").match("realtime"))
                    {
                        if (typeof CurrentCampaign !== 'undefined') {
                            GetRealtime();
                        } else {
                            $(".dropdown-scroll").append("<li><a href='#'>There are no Campaigns created.</li>");
                            $("#kant").html("<br><center><b>No Campaigns.</b></a><img style='margin-left:8px' src='../images/users/icon_emotion_sad_32.png'></center>");
                            $('.dropdown-scroll').slimScroll({
                                position: 'right',
                                height: '30px',
                                railVisible: false,
                                wheelStep: 8
                            });
                            $(".ul-dropdown").css("width", "210px");
                            //CurrentCampaign = undefined; 
                            //$(".current-campaign").html(""); 
                        }
                    }
                    else
                    {
                        $.ajax({
                            type: "GET",
                            url: $(this).attr("pagetoload"),
                            success: function(data) {
                                $("#kant").html(data);
                            }
                        });

                    }


                    $.ajax({
                        type: "POST",
                        url: 'requests.php',
                        data: {zero: "SpeedbarLinks", link_id: $(this).attr("menuid")},
                        success: function(data) {
                            //$(".inner-speedbar").html(data); 
                        }
                    });

                    $(".sidebar-nav").removeClass("active");

                    $(this).closest('a').addClass("active");

                    return false;

                });





            </script>



            <script>
                var User = <?php echo $_SESSION['id_user']; ?>;
                var CurrentMessages = new Array();

                /*$("body").click(function(e){
                 // console.log(e.target);
                 });*/

                $("#temp_trigger").click(function() {
                    /*  console.log(CurrentMessages.admin_global);
                     delete CurrentMessages.admin_global.messages[3];
                     console.log(CurrentMessages.admin_global); */
                });

                var User = <?php echo $_SESSION['id_user']; ?>;
                var CurrentMessages = new Array();
      

                $(".show-messages").live("mousedown", function() {
                    ReadMessagesArray();
                    $('.slimscroll').slimScroll({
                        position: 'right',
                        height: '345px', // 345px
                        railVisible: false,
                        wheelStep: 8

                    });
                    $('.slimscroll2').slimScroll({
                        position: 'right',
                        height: '335px', // 345px
                        railVisible: false,
                        wheelStep: 8

                    });
                });

                $("#campaign-enabler-search").live("input", function() {
                    ReadMessagesArray("enabler");
                });


                $(".read-message").live("click", function() {
                    DeleteMessageArray($(this));
                });

                (function() {

                    //  console.log(CurrentCampaign); 
                    //  console.log(CurrentCampaignID);


                    if (!$("#get-campaign-enabler").parent().hasClass("open")) {
                        BuildNotifications(User); /* console.log("Notifications"); */
                    }
                    PopulateDropDownAndCurrentCampaign(true); /* console.log("Populate"); */

                    setTimeout(arguments.callee, 10000);
                })();


                $(document).ready(function() {
                    BuildNotifications(User);
                    PopulateDropDownAndCurrentCampaign(false);
                    
                   
                    
                    
                });
            </script>    
            <style>
                .border-test { border: 1px solid black; }
            </style>
        </body>
    </html>
<?php } else {
    header("Location: /error_503.html");
} ?>

