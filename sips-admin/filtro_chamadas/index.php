<?php
error_reporting(E_ALL ^ E_DEPRECATED ^ E_NOTICE);
ini_set('display_errors', '1');
require "../functions.php";

$self = count(explode('/', $_SERVER['PHP_SELF']));
for ($i = 0; $i < $self - 2; $i++) {
    $header.="../";
}
define("ROOT", $header);

//includes
require(ROOT . "ini/dbconnect.php");

require_once ('../reservas/func/reserve_utils.php');

$PHP_AUTH_USER=$_SERVER["PHP_AUTH_USER"];
$PHP_AUTH_PW=$_SERVER["PHP_AUTH_PW"];

if ( (strlen($PHP_AUTH_USER)<2) or (strlen($PHP_AUTH_PW)<2) )
	{
	Header("WWW-Authenticate: Basic realm=\"VICI-PROJECTS\"");
	Header("HTTP/1.0 401 Unauthorized");
	exit;
	}
        
$stmt="SELECT user_group from vicidial_users where user='".mysql_real_escape_string($PHP_AUTH_USER)."' and pass='".mysql_real_escape_string($PHP_AUTH_PW)."';";
	$rslt=mysql_query($stmt, $link);
	$row=mysql_fetch_row($rslt);
	$LOGuser_group=$row[0];

    if(mysql_num_rows($rslt)==0) 
	{
	Header("WWW-Authenticate: Basic realm=\"VICI-PROJECTS\"");
	Header("HTTP/1.0 401 Unauthorized");
	exit;
	} 
        
$stmt="SELECT allowed_campaigns from vicidial_user_groups where user_group='$LOGuser_group';";
	$rslt=mysql_query($stmt, $link);
	$row=mysql_fetch_row($rslt);
	$LOGallowed_campaigns =	$row[0];

	$LOGallowed_campaignsSQL='';
	$whereLOGallowed_campaignsSQL='';
	if ( (!eregi("-ALL",$LOGallowed_campaigns)) )
		{
		$rawLOGallowed_campaignsSQL = preg_replace("/ -/",'',$LOGallowed_campaigns);
		$rawLOGallowed_campaignsSQL = preg_replace("/ /","','",$rawLOGallowed_campaignsSQL);
		$LOGallowed_campaignsSQL = "and campaign_id IN('$rawLOGallowed_campaignsSQL')";
		$whereLOGallowed_campaignsSQL = "where campaign_id IN('$rawLOGallowed_campaignsSQL')";
		}
$stmt="SELECT campaign_id,campaign_name from vicidial_campaigns where active='Y' $LOGallowed_campaignsSQL order by campaign_id;";
	$rslt=mysql_query($stmt, $link);
	$IGcampaigns_to_print = mysql_num_rows($rslt);
	$IGcampaign_id_list=array();
	$i=0;
	while ($i < $IGcampaigns_to_print)
		{
		$row=mysql_fetch_row($rslt);
		$campaign_list[$row[0]]= $row[1];
		$i++;
		}
                 

if (checkDateTime($_GET["dt"], "Y-m-d")) {
    $date = (date('D') == 'Mon') ? date("Y-m-d", strtotime($_GET[dt] . " this monday")) : date("Y-m-d", strtotime($_GET[dt] . " last monday"));
} else {
    $date = date("Y-m-d", strtotime($date . ' +1 days')); //(date('D') == 'Mon') ? date("Y-m-d") : date("Y-m-d", strtotime('last monday'));
}

$date_end = date("Y-m-d", strtotime($date . ' +7 days'));

$days = floor((strtotime($date_end) - strtotime($date)) / (60 * 60 * 24));
$days = ($days == 0) ? 1 : $days;
?>
<!DOCTYPE html>
<html>
    <head>
        <META HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=utf-8">
        <title>SIPS</title>

        <script type="text/javascript" src="/jquery/jquery-1.8.3.js"></script>
        <script type="text/javascript" src="/jquery/jsdatatable/js/jquery.dataTables.min.js"></script>
        <script type="text/javascript" src="/bootstrap/js/bootstrap.min.js"></script>
        <script type="text/javascript" src="/bootstrap/js/chosen.jquery.min.js"></script>
        <script type="text/javascript" src="/bootstrap/js/warnings-api.js"></script>
        <link type="text/css" rel="stylesheet" href="/bootstrap/css/style.css" />
        <link type="text/css" rel="stylesheet" href="/bootstrap/css/bootstrap-responsive.css" />
        <link type="text/css" rel="stylesheet" href="/bootstrap/css/bootstrap.css" />
        <link type="text/css" rel="stylesheet" href="/bootstrap/css/chosen.css" />
        <link type="text/css" rel="stylesheet" href="/bootstrap/icon/font-awesome.css" />
        <style>
            .box_title {
                color: #ffffff;}
            .box_title .pull-left {
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
                max-width: 88%;}

            .glow,.glow input{
                cursor: pointer !important;
            }
            
            .glow {
                zoom:0.75;
                -webkit-transition: border linear 0.2s, box-shadow linear 0.2s;
                -moz-transition: border linear 0.2s, box-shadow linear 0.2s;
                -o-transition: border linear 0.2s, box-shadow linear 0.2s;
                transition: border linear 0.2s, box-shadow linear 0.2s;}

            .glow.warning {
                color: #c09853;
                border-color: #c09853;
                -webkit-box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.075);
                -moz-box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.075);
                box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.075);}

            .glow.warning:hover {
                border-color: #a47e3c;
                -webkit-box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.075), 0 0 6px #dbc59e;
                -moz-box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.075), 0 0 6px #dbc59e;
                box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.075), 0 0 6px #dbc59e;}

            .glow.error {
                color: #b94a48;
                border-color: #b94a48;
                -webkit-box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.075);
                -moz-box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.075);
                box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.075);}

            .glow.error:hover {
                border-color: #953b39;
                -webkit-box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.075), 0 0 6px #d59392;
                -moz-box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.075), 0 0 6px #d59392;
                box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.075), 0 0 6px #d59392;}

            .glow.success  {
                color: #468847;
                border-color: #468847;
                -webkit-box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.075);
                -moz-box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.075);
                box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.075);}

            .glow.success:hover {
                border-color: #356635;
                -webkit-box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.075), 0 0 6px #7aba7b;
                -moz-box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.075), 0 0 6px #7aba7b;
                box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.075), 0 0 6px #7aba7b;}

            .cantouchthis,.cantouchthis input{
                -moz-user-select: none; 
                -khtml-user-select: none; 
                -webkit-user-select: none; 
                -o-user-select: none; }

            .green{
                color: green;
                margin: 0.1em;}

            .red{
                color: white;
                margin: 0.1em;}

            .orange{
                color: orangered;
                margin: 0.1em;}
            .min-input{
                width: 2em !important;}
            #loader{
                background: #fff;
                top: 0px;
                left: 0px;
                position: absolute;
                height: 100%;
                width: 100%;
                z-index: 2;
            }
            #loader > img{
                position:absolute;
                left:50%;
                top:50%;
                margin-left: -33px;
                margin-top: -33px;
            }
            div[active="1"]{
            }

            .led-red {
                margin: 16px auto 0;
                width: 12px;
                height: 12px;
                background-color: #940;
                border-radius: 50%;
                box-shadow: #000 0 -1px 7px 1px, inset #600 0 -1px 9px, #F00 0 2px 12px;
            }

            .led-yellow {
                margin: 16px auto 0;
                width: 12px;
                height: 12px;
                background-color: #A90;
                border-radius: 50%;
                box-shadow: #000 0 -1px 7px 1px, inset #660 0 -1px 9px, #DD0 0 2px 12px;
            }

            .led-green {
                margin: 16px auto 0;
                width: 12px;
                height: 12px;
                background-color: #6F0;
                border-radius: 50%;
                box-shadow: #000 0 -1px 7px 1px, inset #490 0 -1px 9px, #7F0 0 2px 12px;
            }

            .led-blue {
                margin: 16px auto 0;
                width: 12px;
                height: 12px;
                background-color: #4AB;
                border-radius: 50%;
                box-shadow: #000 0 -1px 7px 1px, inset #006 0 -1px 9px, #06F 0 2px 14px;
            }


        </style>
    </head>
    <body>
        <div id="loader"><img src="/images/icons/big-loader.gif"/></div>
        <div class="content">   
            <div class="grid">
                <div class="grid-title">
                    <div class="pull-left">Filtro de Branchs e CATOS. De <?= $date . "  a  " . $date_end."." ?> // Total:<b id="total"></b></div>
                    <div class="pull-right"></div>
                    <div class="clear"></div>
                </div>
                <div class="grid-content">
                    <div id="wr"></div>

                    <div class="row-fluid">
                        <p class="span4 text-left">
                                <input type="checkbox" class="b2" id="hoff" checked />
                                <label for="hoff">Desligados<span></span></label>
                                <input type="checkbox" class="b2" id="hon" checked />
                                <label for="hon">Ligados<span></span></label>
                        </p>
                        <p class="span4 text-center">
                            <label for="campaigns_list">Campanhas</label>
                            <select class="chzn-select" data-placeholder="Escolha um Campanha..." id="campaigns_list">
                                <option></option>
                                <?= populate_options($campaign_list, "") ?>
                            </select>
                        </p>
                        <p class="span4 text-right-right">
                                <input type="checkbox" class="b2" id="hred" checked />
                                <label for="hred">Vermelhos<span></span></label>
                                <input type="checkbox" class="b2" id="hyel" checked />
                                <label for="hyel">Laranja<span></span></label>
                                <input type="checkbox" class="b2" id="hgreen" checked />
                                <label for="hgreen">Verdes<span></span></label>
                        </p>
                    </div>
                    <div class="clear"></div>
                    <div id="box-container">
                    </div>
                    <div class="clear"></div>
                </div>
            </div>
        </div>

        <script>
            var total=0;
            var boxes=undefined;
            function filtro(id,target){
                $("#"+id).change(function(){
                    if(this.checked){
                        $(target).removeClass("hide");
                    }else{
                        $(target).addClass("hide");
                    }
                });
                return true;
            }
            
            var activator=function(e){
                if($(this).attr("data-campaign")===""){return false;}
                    console.log($(this).attr("data-resource"));
                    var block=this;
                    $(block).find(".play")
                    .removeClass("icon-pause")
                    .removeClass("icon-play")
                    .addClass("icon-time")
                    .parent()
                    .parent()
                    .parent()
                    .find(".led")
                    .removeClass("led-red")
                    .removeClass("led-green");
                    $.post("ativa.php",
                    {branch:$(this).attr("data-resource"),campaign:$(this).attr("data-campaign")},
                    function(data){
                        if(data.active===1){
                            $(block).attr("data-active",data).find(".play")
                            .removeClass("icon-time")
                            .addClass("icon-pause")
                            .parent()
                            .parent()
                            .parent()
                            .find(".led")
                            .removeClass("led-red")
                            .addClass("led-green");
                    console.log("true");
                        }else{
                            $(block).attr("data-active",data).find(".play")
                            .removeClass("icon-time")
                            .addClass("icon-play")
                            .parent()
                            .parent()
                            .parent()
                            .find(".led")
                            .removeClass("led-green")
                            .addClass("led-red");
                    console.log("fuck");
                        }    
                    },"json").fail(function(){
                    makeAlert("#wr","Ups Ocurreu um erro ao activar/inactivar os CATOS/BRANCHS","Peço desculpa.",1,true,false);
                });
                };
            $(function(){
                
                
                
                filtro("hred",".glow.error");
                filtro("hyel",".glow.warning");
                filtro("hgreen",".glow.success");
                
                filtro("hoff","div[data-active=0]");
                filtro("hon","div[data-active=1]");
                
                
                   
                
                $(".chzn-select").chosen({no_results_text: "Não foi encontrado."});
                
            
                $.post("boxes.php",{user:"<?=$PHP_AUTH_USER?>",pass:"<?=$PHP_AUTH_PW?>",data_i:"<?= $date?>",data_f:"<?= $date_end?>",dias:<?= $days?>},
                function(data){
                    $("#total").text(data.total);
                    $("#box-container").html(data.boxes);
                    $("[data-t=tooltip]").tooltip();
                    boxes=$("#box-container > div[data-resource]");
                $("#loader").fadeOut("slow");
                boxes.click(activator);
                },"json")
                        .fail(function(){
                    makeAlert("#wr","Ups Ocurreu um erro ao carregar os CATOS/BRANCHS","Peço desculpa.",1,true,false);
                        });
                
                $("#campaigns_list").change(function(){
                var camp=$(this).val();
                
                $.post("boxes.php",{user:"<?=$PHP_AUTH_USER?>",pass:"<?=$PHP_AUTH_PW?>",campaign:camp},function(data){
                    boxes.each(function(index){
                       $(this).attr("data-campaign",camp).attr("data-active",0).find(".play").attr("class","play icon-play").parent().parent().parent().find(".led").attr("class","pull-right led led-red"); 
                    });
                    $(data).each(function(index){
                       boxes.parent().find("[data-resource="+this+"]").attr("data-active",1).find(".play").attr("class","play icon-pause").parent().parent().parent().find(".led").attr("class","pull-right led led-green");
                    });
                },"json")
                        .fail(function(){
                    makeAlert("#wr","Ups Ocurreu um erro ao carregar os CATOS/BRANCHS","Peço desculpa.",1,true,false);
                        });
                });
            });
        </script>
    </body>
</html>
