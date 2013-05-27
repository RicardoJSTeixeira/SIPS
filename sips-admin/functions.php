<?php
# 
# functions.php    version 2.2.0
#
# functions for administrative scripts and reports
#
# Copyright (C) 2009  Matt Florell <vicidial@gmail.com>    LICENSE: AGPLv2
#
#
# CHANGES:
# 90524-1503 - First Build
#

##### reformat seconds into HH:MM:SS or MM:SS #####
function sec_convert($sec,$precision)
	{
	$sec = round($sec,0);

	if ($sec < 1 and $precision='H')
		{
		return "00:00:00";
		}
	elseif ($sec < 1 and $precision='M')
		{
                return "00:00";
                }
	else
		{
		//if ($sec < 3600) {$precision='M';}

		if ($precision == 'H')
			{
			$Fhours_H =	($sec / 3600);
			$Fhours_H_int = floor($Fhours_H);
			$Fhours_H_int = intval("$Fhours_H_int");
			$Fhours_M = ($Fhours_H - $Fhours_H_int);
			$Fhours_M = ($Fhours_M * 60);
			$Fhours_M_int = floor($Fhours_M);
			$Fhours_M_int = intval("$Fhours_M_int");
			$Fhours_S = ($Fhours_M - $Fhours_M_int);
			$Fhours_S = ($Fhours_S * 60);
			$Fhours_S = round($Fhours_S, 0);
			if ($Fhours_S < 10) {$Fhours_S = "0$Fhours_S";}
			if ($Fhours_M_int < 10) {$Fhours_M_int = "0$Fhours_M_int";}
			if ($Fhours_H_int < 10) {$Fhours_H_int = "0$Fhours_H_int";}
			$Ftime = "$Fhours_H_int:$Fhours_M_int:$Fhours_S";
			}
		if ($precision == 'M')
			{
			$Fminutes_M = ($sec / 60);
			$Fminutes_M_int = floor($Fminutes_M);
			$Fminutes_M_int = intval("$Fminutes_M_int");
			$Fminutes_S = ($Fminutes_M - $Fminutes_M_int);
			$Fminutes_S = ($Fminutes_S * 60);
			$Fminutes_S = round($Fminutes_S, 0);
			if ($Fminutes_S < 10) {$Fminutes_S = "0$Fminutes_S";}
			$Ftime = "$Fminutes_M_int:$Fminutes_S";
			}
		if ($precision == 'S')
			{
			$Ftime = $sec;
			}
		return "$Ftime";
		}
	}


##### counts like elements in an array, optional sort asc desc #####
function array_group_count($array, $sort = false) 
	{
	$tally_array = array();

	$i=0;
	foreach (array_unique($array) as $value) 
		{
		$count = 0;
		foreach ($array as $element) 
			{
		    if ($element == "$value")
		        {$count++;}
			}

		$count =		sprintf("%010s", $count);
		$tally_array[$i] = "$count $value";
		$i++;
		}
	
	if ( $sort == 'desc' )
		{rsort($tally_array);}
	elseif ( $sort == 'asc' )
		{sort($tally_array);}

	return $tally_array;
	}
        
        function populate_options($array, $selected, $keys = true) {
                $populus = "";
                if ($keys) {
                    foreach ($array as $key => $value) {
                        $populus.="<option value='$key'" . (($key == $selected) ? "SELECTED" : "") . " >$value</option>";
                    }
                } else {
                    foreach ($array as $value) {
                        $populus.="<option value='$value'" . (($value == $selected) ? "SELECTED" : "") . " >$value</option>";
                    }
                }
                return $populus;
            }
        function populate_multi_options($array, $selecteds, $keys = true) {
                $populus = "";
                if ($keys) {
                    foreach ($array as $key => $value) {
                        $populus.="<option value='$key'" . ((in_array($key,$selecteds)) ? "SELECTED" : "") . " >$value</option>";
                    }
                } else {
                    foreach ($array as $value) {
                        $populus.="<option value='$value'" . ((in_array($value,$selecteds)) ? "SELECTED" : "") . " >$value</option>";
                    }
                }
                return $populus;
            }

?>