<?php
require("../ini/dbconnect.php");
require("../ini/user.php");

$user = new user;
if (!$user->id) {

    header("location: ../index.php");
}

if ($user->user_level < 6) {
    header("location: ../index.php?logout=yes");
}

    $connection = mysql_connect("$VARDB_server:$VARDB_port", "sipsadmin", "sipsps2012");
    mysql_select_db("asterisk", $connection);
    
    mysql_query("ALTER TABLE `vicidial_campaigns` ADD `agent_allow_copy_record` TINYINT(1) NOT NULL DEFAULT '0' ;") ;
    
    mysql_close($connection);

$curlogo=$_POST['curlogo'];
?>
<!DOCTYPE html>
<html>
    <head>
        <META HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=utf-8">
        <title>Go Contact Center</title>
        <link type="text/css" rel="stylesheet" title="sipsdefault" href="/css/style.css" />
        <link type="text/css" rel="stylesheet" href="/bootstrap/css/bootstrap.css" />
        <link type="text/css" rel="stylesheet" href="/bootstrap/css/bootstrap-responsive.css" />

        <style>
            .content {
                margin-left: 10px;
                margin-right: 10px;
            }
            .row-fluid [class*="span"]:first-child, .row-fluid .span12 {
                margin-left: 0 !important;
            }
            iframe {display: block; width: 100%; border: none; overflow-y: auto; overflow-x: hidden;} 
            .cc-menu{width: 100%;padding:0;}
            .cc-menu .pull-left{text-overflow: ellipsis;white-space: nowrap;overflow: hidden;max-width: 70%}
            .cc-menu .grid-title {padding: 4px 0px 0px 6px;}
            .cc-submenu{ width: 95%;margin-left:2.5%;margin-right:2.5%;}


            #cc-header {

                background:0 !important;
                border-bottom: 3px solid rgb(168, 168, 168) !important;
                border-top:none !important;
                border-left:none !important;
                border-right:none !important;
                border-radius: 0px !important;
                -webkit-box-shadow:none !important;
                box-shadow:none !important;

            }

            .cc-menu {

                color: rgb(105, 105, 105) !important;
                background:0 !important;
                border-bottom: 2px solid rgb(192, 192, 192) !important;
                border-top:none !important;
                border-left:none !important;
                border-right:none !important;
                border-radius: 0px !important;
                -webkit-box-shadow:none !important;
                box-shadow:none !important;
                text-shadow:none !important;
            } 

            .cc-submenu table tbody tr { border-color: #c0c0c0 !important; }
            .cc-submenu tr:hover { background-color: #e2e2e2 !important;  }
            .cc-mstyle { border-color: #c0c0c0 !important; }

            body {
                background:none !important; 
            }
            #mbody {
                min-height: 80%;
            }


        </style>
    </head>
    <body id="ib">

        <div class="container-fluid">

            <div id='cc-header' class="row-fluid">
                <div class="span5">
                        <img class="left" src='/images/pictures/go_logo_15.png' id="menu-hide" > 
                </div>

                <div class="span7">
                    <a href="../index.php?logout=yes" ><img style='height:75px;float:right;' src='/images/pictures/cute_cloud3.png' /></a>
                </div>
            </div>

            <div class="row-fluid">
                <div class="span2" id="menu"   style='margin-top:1px;'>

                    <DIV id="menu-content">
                        <div class='cc-menu'>
                            <div class="grid-title">
                                <div class="pull-left tooltip-top offset1" data-t="tooltip" title="Menu">Menu</div>
                                <div class="pull-right"><img class='cc-menu-img' id='img1'  src='/images/icons/headphone_mic_16.png' /></div>
                                <div class="clear"></div>   
                            </div>
                        </div>
                        <div class='cc-submenu' style='display:block'>

                            <table>
                                <tbody>
                                    <?php
                                    $smt = "SELECT `url`, `imgpath`, `label`, `grupo` FROM sips_admin_links WHERE grupo = '$user->user_group'";

                                    $rslt = mysql_query($smt, $link) or die(mysql_error());

                                    if (!mysql_num_rows($rslt)) {
                                        $smt = "SELECT `url`, `imgpath`, `label`, `grupo` FROM sips_admin_links WHERE grupo = 'ALL_GROUPS'";
                                        $rslt = mysql_query($smt, $link) or die(mysql_error());
                                    }
                                    for ($i = 0; $i < mysql_num_rows($rslt); $i++) {

                                        $curLink = mysql_fetch_assoc($rslt);
                                        $admin_link = $curLink['url'];
                                        ?>
                                        <tr  onclick="open_page('<?= $admin_link . (preg_match("/\?/", $admin_link) ? "&" : "?") . time() ?>');" >
                                            <td><img src='<?= $curLink[imgpath] ?>' /></td>
                                            <td><?= $curLink[label] ?></td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>


                        <?php
                        $query = "Select `id_menu_group`,`title`,`img`,`allowedgroups` From sips_admin_menu_groups WHERE active=1 order by `order` ASC";
                        $rslt = mysql_query($query, $link) or die(mysql_error());

                        $groups = array();
                        while ($row = mysql_fetch_assoc($rslt)) {
                            if (preg_match("/\b" . $user->user_group . "\b/", $row[allowedgroups]) OR preg_match("/\bALL_GROUPS\b/", $row[allowedgroups])) {
                                $groups[] = $row;
                                $in_groups.="'" . $row[id_menu_group] . "',";
                            }
                        }

                        $groups = array_values($groups);
                        $in_groups = rtrim($in_groups, ",");
                        if (strlen($in_groups) > 0) {
                            $query = "Select `title`,`img`,`url`,`allowedgroups`,`id_menu_groups` From sips_admin_menu_links WHERE `id_menu_groups` IN ($in_groups) AND active=1 order by `order` ASC";
                            $rslt = mysql_query($query, $link) or die(mysql_error());

                            $links = array();
                            for ($i = 0; $i < mysql_num_rows($rslt); $i++) {
                                $row = mysql_fetch_assoc($rslt);
                                if (preg_match("/\b" . $user->user_group . "\b/", $row[allowedgroups]) OR preg_match("/\bALL_GROUPS\b/", $row[allowedgroups])) {
                                    $links[$i] = $row;
                                }
                            }

                            $links = array_values($links);
                            for ($i = 0; $i < count($groups); $i++) {
                                ?>
                                <div class='cc-menu' >
                                    <div class="grid-title">
                                        <div class="pull-left  tooltip-top offset1" data-t="tooltip" title="<?= $groups[$i][title] ?>"><?= $groups[$i][title] ?></div>
                                        <div class="pull-right"><img class='cc-menu-img' id='img1'  src='/images/icons/<?= $groups[$i][img] ?>' /></div>
                                        <div class="clear"></div>   
                                    </div>
                                </div>

                                <div class='cc-submenu' >
                                    <table>
                                        <?php
                                        for ($ii = 0; $ii < count($links); $ii++) {
                                            if ($links[$ii][id_menu_groups] == $groups[$i][id_menu_group]) {
                                                ?>
                                                <tr onClick="open_page('<?= $links[$ii]['url'] . (preg_match("/\?/", $links[$ii]['url']) ? "&" : "?") . time() ?>')">
                                                    <td><img src='/images/icons/<?= $links[$ii][img] ?>' /></td><td><?= $links[$ii][title] ?></td>
                                                </tr>
                                                <?php
                                                unset($links[$ii]);
                                                $links = array_values($links);
                                                $ii--;
                                            }
                                        }
                                        ?>
                                    </table>
                                </div>
                                <?php
                            }
                        }
                        ?>
                    </DIV>
                </DIV>
                <div class="span10" id="iframe-conteiner">
                    <iframe name="mbody" id="mbody" src='<?= $admin_link ?>' ></iframe>
                </div>
            </div>
        </div>

        <script src="/jquery/jquery-1.9.1.js"></script>
        <script src="/jquery/jqueryUI/jquery-ui-1.10.2.custom.min.js"></script>
        <script>
$.fn.toggleClick = function() {
  var events = arguments;
  var iteration = 0;
  
  return $(this).click( function() {
        events[iteration].apply(this, arguments);
	iteration = (iteration + 1) % events.length;
  });
  
};



                                        function setCookie(key, value) {
                                            var expires = new Date();
                                            expires.setTime(expires.getTime() + 31536000000); //1 year  
                                            document.cookie = key + '=' + value + ';expires=' + expires.toUTCString();
                                        }

                                        function open_page(url) {
                                            $("#mbody").attr('src', url);
                                            document.cookie = setCookie("pagina", url);
                                        }

                                        function getCookie(key) {
                                            var keyValue = document.cookie.match('(^|;) ?' + key + '=([^;]*)(;|$)');
                                            return keyValue ? keyValue[2] : null;
                                        }

                                        function SetFrameHeight() {
                                            $("#mbody").height($(window).height() - 90);
                                        }

                                        window.onresize = function() {
                                            SetFrameHeight();
                                        };

                                        $(function() {
                                            SetFrameHeight();
                                            $("body").on("close", function() {
                                                document.cookie = setCookie("pagina", null);
                                            });
                                            $('#menu-content > .cc-submenu').hide();


                                            var checkCookie = getCookie("separador");

                                            if (checkCookie !== "") {
                                                $('#menu-content > .cc-menu:eq(' + checkCookie + ')').next().show();
                                            }

                                            var checkPagina = getCookie("pagina");

                                            if (checkPagina !== null) {
                                                $("#mbody").attr("src", checkPagina);
                                            }

                                            $('#menu-content > .cc-menu').click(function() {

                                                document.cookie = setCookie("separador", $("#menu-content > .cc-menu").index(this));


                                                $(this).next('.cc-submenu').slideToggle('fast')
                                                        .siblings('.cc-submenu:visible').slideUp('fast');
                                            });

                                            $("#menu-hide").toggleClick(
                                                    function() {
                                                        $("#iframe-conteiner").removeClass("span10").addClass("span12");
                                                    $("#menu").css("position", "absolute").stop(true, true).hide('slide', {direction: "left"});
                                                    },
                                                    function() {
                                                        $("#menu").stop(true, true).show('slide', {direction: "left"}, function() {
                                                            $(this).css("position", "static");
                                                            $("#iframe-conteiner").removeClass("span12").addClass("span10");
                                                        });
                                                        
                                                    });

                                        });


        </script>
    </body>
</html>
