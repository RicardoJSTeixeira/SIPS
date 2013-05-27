<?php
date_default_timezone_set('Europe/Lisbon');
# Converte a data em formato portugues, para o formato de MySQL
function DatePT2DateSQL($data) {
	$tExplode = explode("/", $data);
	$rData = $tExplode[2]."-".$tExplode[1]."-".$tExplode[0];
	return $rData;
}
#Converte uma data em formato MySQL para formato portugues | "DD/MM/AAAA às HH:MM:SS"
function DateSQL2DatePT($data) {
	$tExplode = explode(" ", $data);
	$ttExplode = explode("-", $tExplode[0]);
	$rData = $ttExplode[2]."-".$ttExplode[1]."-".$ttExplode[0]." às ".$tExplode[1];
	return $rData;
}
# Recebe uma query e o index que queremos concatenar no formato "IN($result)" 
function Query2IN($query, $index) {
	$num_rows = mysql_num_rows($query);
	if($num_rows!=0){	
		for ($i=0;$i<$num_rows;$i++)
		{
			$row = mysql_fetch_row($query);
			if ($num_rows == 1) 
				{
				$result = "'".$row[$index]."'"; 
				}
			elseif ($num_rows-1 == $i)
				{
				$result .= "'".$row[$index]."'";	
				}	
			else
				{
				$result .= "'".$row[$index]."',";
				}	
		}
		return $result; 
	} else { return "''";}
}
# Recebe uma query e o index que queremos concatenar no formato "IN($result)" 
function Array2IN($array) {
    $ArrayCount = count($array);
    if($ArrayCount!=0){    
        for ($i=0;$i<$ArrayCount;$i++)
        {
            if ($ArrayCount == 1) 
                {
                $result = "'".$array[$i]."'"; 
                }
            elseif ($ArrayCount - 1 == $i)
                {
                $result .= "'".$array[$i]."'";    
                }    
            else
                {
                $result .= "'".$array[$i]."',";
                }    
        }
        return $result; 
    } else { return "''";}
}





function NumberPT($number)
{
	$number = number_format($number, 0, ',', ' ');
	return $number;
}
# Update à admin_log sempre que o admin altera algo nas definições do SIPS
/* Event types = 'ADD', 'COPY', 'LOAD', 'RESET', 'MODIFY', 'DELETE', 'SEARCH', 'LOGIN', 'LOGOUT', 'CLEAR', 'OVERRIDE', 'EXPORT', 'OTHER' */

function AdminLogger($event_section, $event_type, $record_id, $event_code, $event_sql, $event_notes)
{
	$event_date = date("Y-m-d H:i:s");
	$user = $_SERVER['PHP_AUTH_USER'];
	$ip_address = $_SERVER["REMOTE_ADDR"];
	$sQuery = "INSERT INTO vicidial_admin_log (event_date, user, ip_address, event_section, event_type, record_id, event_code, event_sql, event_notes) VALUES ('$event_date', '$user', '$ip_address', '$event_section', '$event_type', '$record_id', '$event_code', \"$event_sql\", '$event_notes')";
	mysql_query($sQuery);
		
}
?>