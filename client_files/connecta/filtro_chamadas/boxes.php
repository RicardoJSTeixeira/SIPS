<?php
error_reporting(E_ALL ^ E_DEPRECATED ^ E_NOTICE);
ini_set('display_errors', '1');
$self = count(explode('/', $_SERVER['PHP_SELF']));
for ($i = 0; $i < $self - 2; $i++) {
    $header.="../";
}
define("ROOT", $header);

//includes
require(ROOT . "ini/dbconnect.php");
require(ROOT . "ini/user.php");
$user=new user;

$campaign_id=$_POST["campaign"];

if ( !$user->id)
	{
	Header("WWW-Authenticate: Basic realm=\"SIPS\"");
	Header("HTTP/1.0 401 Unauthorized");
	exit;
	}

        
if(isset($_POST["campaign"])){
     
    $box_stats=array();
        $query = "SELECT id_resource FROM `sips_sd_filter` WHERE  campaign_id='".mysql_real_escape_string($campaign_id)."'";
        $rs = mysql_query($query, $link) or die(mysql_error());
        while ($row = mysql_fetch_row($rs)) {
        $box_stats[]=$row[0];
        }
        echo json_encode($box_stats);
        exit;
}        

     $date=$_POST["data_i"];
     $date_end=$_POST["data_f"];
     $days=$_POST["dias"];
    

require_once (ROOT.'sips-admin/reservas/func/reserve_utils.php');

$total = 0;

echo json_encode(array("boxes"=>cria_listagem(),"total"=>$total));

function cria_listagem() {
    global $total;
    global $link;
    global $date;
    global $date_end;
    global $days;

    $date_orginal = $date;

    $labels = array("Concluido" => "label-success", "Medio" => "label-warning", "Fraco" => "label-important");
    $labels_ref = array("Fraco", "Medio", "Concluido");
    $colors = array("red", "orange", "green");

    $color_head = array("Concluido" => "success", "Medio" => "warning", "Fraco" => "error");

    $query = "SELECT b.id_resource, b.display_text, b.alias_code, a.blocks, a.begin_time, a.end_time+a.blocks end_time, count(c.id_resource) 'marc', b.`restrict_days` FROM `sips_sd_schedulers` a INNER JOIN `sips_sd_resources` b ON a.id_scheduler=b.id_scheduler left JOIN `sips_sd_reservations` c ON b.id_resource=c.id_resource and a.active=1 and b.active=1 and start_date between '$date' and '$date_end' group by b.id_resource";
    $result = mysql_query($query, $link) or die(mysql_error($link));
$boxes="";
    //começa a calcular as marcações. CADA CICLO CORRESPONDE A UM RESOURCE!
    for ($index = 0; $index < mysql_num_rows($result); $index++) {
        $schdl = mysql_fetch_assoc($result);
        $inverted = $schdl["restrict_days"];
        $date = $date_orginal;
        $slots = 0;

        //coloca uma todas as series num array
        $qry = "SELECT `start_time`, `end_time`, `day_of_week_start`, `day_of_week_end` FROM `sips_sd_series` WHERE `id_resource` =$schdl[id_resource]";
        $rslts = mysql_query($qry, $link) or die(mysql_error($link));
        $series = array();
        for ($index1 = 0; $index1 < mysql_num_rows($rslts); $index1++) {
            $series[] = mysql_fetch_assoc($rslts);
        }

        //coloca uma todas as execoes num array
        $qry = "SELECT `start_date`, `end_date` FROM `sips_sd_execoes` WHERE `id_resource` = $schdl[id_resource]";
        $rslts = mysql_query($qry, $link) or die(mysql_error($link));
        $execoes = array();
        for ($index1 = 0; $index1 < mysql_num_rows($rslts); $index1++) {
            $execoes[] = mysql_fetch_assoc($rslts);
        }


        for ($ii = 0; $ii < $days; $ii++) {
            for ($i = $schdl["begin_time"]; $i < $schdl["end_time"]; $i += $schdl["blocks"]) {
                $slots+= ($inverted) ? 0 : 1;
                $beg = date("Y-m-d H:i:s", strtotime($date . "+$i minutes"));

                //check se está bloqueada por uma exepção
                for ($k = 0; $k < count($execoes); $k++) {
                    if (checkExecoesDate($beg, $execoes[$k]["start_date"], $execoes[$k]["end_date"])) {
                        $slots+= ($inverted) ? 1 : -1;
                        $salta = true;
                        break;
                    }
                }

                if ($salta) {
                    $salta = false;
                    continue;
                }

                //check se está bloqueada por uma serie
                for ($j = 0; $j < count($series); $j++) {
                    if (checkSeriesDate($beg, $series[$j]["start_time"], $series[$j]["day_of_week_start"], $series[$j]["end_time"], $series[$j]["day_of_week_end"])) {
                        $slots+= ($inverted) ? 1 : -1;
                        break;
                    }
                }
            }
            $date = date("Y-m-d", strtotime($date . " +1 day"));
        }

        $total+=$schdl["marc"];

        $max = $slots;

        if ($slots){
          $perc = round($schdl["marc"] * 100 / $max);
        $range = (($perc < 33) ? 0 : (($perc < 66) ? 1 : 2));
  
        }else{
            $perc=100;
            $range=3;
        }
          
        
        $boxes.="<div class='grid span2 cantouchthis glow ".$color_head[$labels_ref[$range]]."' data-resource='$schdl[id_resource]' data-campaign='' data-active='' >
            <div class='grid-title box_title ".$labels[$labels_ref[$range]]."'>
                <div class='pull-left  tooltip-top' data-t='tooltip' title='$schdl[display_text]'>$schdl[display_text]</div>
                <div class='pull-right'><i class='play '></i></div>
                <div class='clear'></div>   
            </div>

            <div class='grid-content ".((round($perc) < 33) ? $labels['Fraco'] : '')."'>
                <div class='btn-modal'>
                    <span class='control-group ".$color_head[$labels_ref[$range]]."'>
                        <input type='text' readonly='' class='min-input' value='$schdl[marc]'>
                        <span><i class='icon-hand-right $colors[$range]'></i></span>
                        <input type='text' readonly='' class='min-input' value='$max'>
                    </span>
                    <span style='display:inline' class='$colors[$range]'>".round($perc)."%</span>
                    <span class='pull-right led'></span>
                </div>
            </div>
        </div>";
    }
return $boxes;
}
