<?
#HEADER
$self = count(explode('/', $_SERVER['PHP_SELF']));
for ($i = 0; $i < $self - 2; $i++) {
    $header.="../";
}
define("ROOT", $header);
require(ROOT . "ini/header.php");



if (!isset($_SERVER[PHP_AUTH_USER])) {
    echo "<script> window.location='../index.php'</script>";
}


$username = $_SERVER[PHP_AUTH_USER];
$password = $_SERVER[PHP_AUTH_PW];
$query = "SELECT user_level FROM vicidial_users where user='$username' and pass='$password'";
$query = mysql_query($query, $link);
if (mysql_num_rows($query)) {
    $row = mysql_fetch_row($query);
    if ($row[0] < 6) {
        echo "<script> window.location='../index.php?logout=yes'</script>";
    }
} else {
    echo "<script> window.location='../index.php?logout=yes'</script>";
}
?>

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
	
	
	
</style>
</head>
<body id="ib">

    <div class="container-fluid">

        <div id='cc-header' class="row-fluid">
            <div class="span3">
                <img style='float:left' src='/images/pictures/go_logo_15.png' id="menu-hide" >
            </div>
            <div class="span9">
                <a href="../index.php?logout=yes" ><img style='height:75px;float:right;' src='/images/pictures/cute_cloud3.png' /></a>
            </div>
        </div>

        <div class="row-fluid">
            <div class="span2" id="menu"   style='margin-top:1px;'><!-- MENU -->
                
                <DIV id="menu-content">
                <div class='cc-menu'>
                    <div class="grid-title">
                        <div class="pull-left  tooltip-top offset1" data-t="tooltip" title="Menu">Menu</div>
                        <div class="pull-right"><img class='cc-menu-img' id='img1'  src='/images/icons/headphone_mic_16.png' /></div>
                        <div class="clear"></div>   
                    </div>
                </div>
                <div class='cc-submenu' style='display:block'>

                    <table>
                        <tbody>
                        <?php
                        $query = "SELECT user_level, user_group FROM vicidial_users where user='$_SERVER[PHP_AUTH_USER]';";
                        $query = mysql_query($query, $link);
                        $row = mysql_fetch_assoc($query);
                        $usrgrp = $row[user_group];

                        $smt = "SELECT `url`, `imgpath`, `label`, `grupo` FROM sips_admin_links WHERE grupo = '$usrgrp'";

                        $rslt = mysql_query($smt, $link) or die(mysql_error());

                        if (!mysql_num_rows($rslt)) {
                            $smt = "SELECT `url`, `imgpath`, `label`, `grupo` FROM sips_admin_links WHERE grupo = 'ALL_GROUPS'";
                            $rslt = mysql_query($smt, $link) or die(mysql_error());
                        }
                        for ($i = 0; $i < mysql_num_rows($rslt); $i++) {

                            $curLink = mysql_fetch_assoc($rslt);
                            $admin_link=$curLink['url'];
                            ?>
                            <tr  onclick=open_page('<?=$admin_link?>'); >
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
                    for ($i = 0; $i < mysql_num_rows($rslt); $i++) {
                        $row = mysql_fetch_assoc($rslt);
                        if (preg_match("/\b" . $usrgrp . "\b/", $row[allowedgroups]) OR preg_match("/\bALL_GROUPS\b/", $row[allowedgroups])) {
                            $groups[$i] = $row;
                            $in_groups.="'" . $row[id_menu_group] . "',";
                        }
                    }

                    $groups = array_values($groups);
                    $in_groups = rtrim($in_groups, ",")
                    ;
                    if (strlen($in_groups) > 0) {
                        $query = "Select `title`,`img`,`url`,`allowedgroups`,`id_menu_groups` From sips_admin_menu_links WHERE `id_menu_groups` IN ($in_groups) AND active=1 order by `order` ASC";
                        $rslt = mysql_query($query, $link) or die(mysql_error());

                        $links = array();
                        for ($i = 0; $i < mysql_num_rows($rslt); $i++) {
                            $row = mysql_fetch_assoc($rslt);
                            if (preg_match("/\b" . $usrgrp . "\b/", $row[allowedgroups]) OR preg_match("/\bALL_GROUPS\b/", $row[allowedgroups])) {
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
                                            <tr onClick="open_page('<?= $links[$ii][url] ?>')">
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

        <script>

                                        function SetFrameHeight() {

                                          $("#mbody").height($(window).height() - 90);

                                        }

                                        window.onresize=function(){SetFrameHeight();};

                                        function ConfirmDelete(Link, Href) {

                                            var rsp = confirm('De certeza que quer eliminar este item ?');
                                            if (rsp) {
                                                Link.href = Href;
                                            }
                                            ;

                                        }
                                        
                                        function open_page(url) {
                                            $("#mbody").attr('src', url);
                                        }
                                        
                                        function setCookie(key, value) {  
                                            var expires = new Date();  
                                            expires.setTime(expires.getTime() + 31536000000); //1 year  
                                            document.cookie = key + '=' + value + ';expires=' + expires.toUTCString();  
                                            }  

                                         function getCookie(key) {  
                                            var keyValue = document.cookie.match('(^|;) ?' + key + '=([^;]*)(;|$)');  
                                            return keyValue ? keyValue[2] : null;  
                                            }  





                                        $(function() {
                                            $('#menu-content > .cc-submenu').hide();
                                            $('#menu-content > .cc-menu').click(function() {

                                                document.cookie = setCookie("separador",$("#menu-content > .cc-menu").index(this));


                                                $(this).next('.cc-submenu').slideToggle('fast')
                                                        .siblings('.cc-submenu:visible').slideUp('fast');
                                            });

                                            var checkCookie = getCookie("separador");

                                            if (checkCookie !== "") {
                                                $('#menu-content > .cc-menu:eq(' + checkCookie + ')').next().show();
                                            }



                                            $("#menu-hide").toggle(
                                                    function() {
                                                        $("#iframe-conteiner").removeClass("span10").addClass("span12");
                                                        $("#menu").css("position", "absolute").stop(true, true).hide('slide', {direction: "left"});
                                                    },
                                                    function() {
                                                        $("#menu").stop(true, true).show('slide', {direction: "left"}, function() {
                                                            $(this).css("position", "static");
                                                        });
                                                        $("#iframe-conteiner").removeClass("span12").addClass("span10");
                                                    });


                                            SetFrameHeight();
                                        });


        </script>
</body>
</html>
