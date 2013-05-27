<?php

function __json_encode( $data ) {           
    if( is_array($data) || is_object($data) ) {
        $islist = is_array($data) && ( empty($data) || array_keys($data) === range(0,count($data)-1) );
       
        if( $islist ) {
            $json = '[' . implode(',', array_map('__json_encode', $data) ) . ']';
        } else {
            $items = Array();
            foreach( $data as $key => $value ) {
                $items[] = __json_encode("$key") . ':' . __json_encode($value);
            }
            $json = '{' . implode(',', $items) . '}';
        }
    } elseif( is_string($data) ) {
        # Escape non-printable or Non-ASCII characters.
        # I also put the \\ character first, as suggested in comments on the 'addclashes' page.
        $string = '"' . addcslashes($data, "\\\"\n\r\t/" . chr(8) . chr(12)) . '"';
        $json    = '';
        $len    = strlen($string);
        # Convert UTF-8 to Hexadecimal Codepoints.
        for( $i = 0; $i < $len; $i++ ) {
           
            $char = $string[$i];
            $c1 = ord($char);
           
            # Single byte;
            if( $c1 <128 ) {
                $json .= ($c1 > 31) ? $char : sprintf("\\u%04x", $c1);
                continue;
            }
           
            # Double byte
            $c2 = ord($string[++$i]);
            if ( ($c1 & 32) === 0 ) {
                $json .= sprintf("\\u%04x", ($c1 - 192) * 64 + $c2 - 128);
                continue;
            }
           
            # Triple
            $c3 = ord($string[++$i]);
            if( ($c1 & 16) === 0 ) {
                $json .= sprintf("\\u%04x", (($c1 - 224) <<12) + (($c2 - 128) << 6) + ($c3 - 128));
                continue;
            }
               
            # Quadruple
            $c4 = ord($string[++$i]);
            if( ($c1 & 8 ) === 0 ) {
                $u = (($c1 & 15) << 2) + (($c2>>4) & 3) - 1;
           
                $w1 = (54<<10) + ($u<<6) + (($c2 & 15) << 2) + (($c3>>4) & 3);
                $w2 = (55<<10) + (($c3 & 15)<<6) + ($c4-128);
                $json .= sprintf("\\u%04x\\u%04x", $w1, $w2);
            }
        }
    } else {
        # int, floats, bools, null
        $json = strtolower(var_export( $data, true ));
    }
    return $json;
}



 header('content-type: text/json');

if (isset($_POST['q'])){$q=$_POST['q'];}
elseif(isset($_GET['q'])){$q=$_GET['q'];}
else{exit;}
require ("dbconnect.php");
	
	$sth = mysql_query("SELECT Name,Display_name,readonly,active FROM `vicidial_list_ref` WHERE  `campaign_id` LIKE  '".mysql_real_escape_string($q)."' AND Name not in ('SOURCE_ID', 'LIST_ID', 'PHONE_CODE', 'GENDER', 'RANK', 'OWNER')  ORDER BY field_order;",$link);

	if (mysql_num_rows($sth)==0)
	{
		echo __json_encode(array(array( Name=>"VENDOR_LEAD_CODE",Display_name=>"", readonly=>"0", active=>"0"),array( Name=>"PHONE_NUMBER", Display_name=>"Nº Telefone", readonly=>"1",active=>"1"),array(Name=>"TITLE", Display_name=>"", readonly=>"0", active=>"0"),array( Name=>"FIRST_NAME", Display_name=>"Nome", readonly=>"0",active=>"1"),array( Name=>"MIDDLE_INITIAL", Display_name=>"Número de Contribuinte", readonly=>"0",active=>"1"),array( Name=>"LAST_NAME", Display_name=>"", readonly=>"0", active=>"0"),array( Name=>"ADDRESS1", Display_name=>"Morada", readonly=>"0",active=>"1"),array( Name=>"ADDRESS2", Display_name=>"", readonly=>"0", active=>"0"),array( Name=>"ADDRESS3", Display_name=>"Telefone Alternativo 2", readonly=>"0", active=>"1"),array( Name=>"CITY", Display_name=>"Localidade", readonly=>"0", active=>"1"),array( Name=>"STATE", Display_name=>"Distrito", readonly=>"0", active=>"1"),array( Name=>"PROVINCE", Display_name=>"", readonly=>"0", active=>"0"),array( Name=>"POSTAL_CODE", Display_name=>"Codigo Postal", readonly=>"0", active=>"1"),array( Name=>"COUNTRY_CODE", Display_name=>"", readonly=>"0", active=>"0"),array( Name=>"DATE_OF_BIRTH", Display_name=>"", readonly=>"0", active=>"0"),array( Name=>"ALT_PHONE", Display_name=>"Telefone Alternativo", readonly=>"0", active=>"1"),array( Name=>"EMAIL", Display_name=>"", readonly=>"0", active=>"0"),array( Name=>"SECURITY_PHRASE", Display_name=>"", readonly=>"0", active=>"0"),array( Name=>"COMMENTS", Display_name=>"Comentários", readonly=>"0", active=>"1"),array( Name=>"extra1", Display_name=>"", readonly=>"0", active=>"0"),array( Name=>"extra2", Display_name=>"", readonly=>"0", active=>"0"),array( Name=>"extra3", Display_name=>"", readonly=>"0", active=>"0"),array( Name=>"extra4", Display_name=>"", readonly=>"0", active=>"0"),array( Name=>"extra5", Display_name=>"", readonly=>"0", active=>"0"),array( Name=>"extra6", Display_name=>"", readonly=>"0", active=>"0"),array( Name=>"extra7", Display_name=>"", readonly=>"0", active=>"0"),array( Name=>"extra8", Display_name=>"", readonly=>"0", active=>"0"),array( Name=>"extra9", Display_name=>"", readonly=>"0", active=>"0"),array( Name=>"extra10", Display_name=>"", readonly=>"0", active=>"0"),array( Name=>"extra11", Display_name=>"", readonly=>"0", active=>"0"),array( Name=>"extra12", Display_name=>"", readonly=>"0", active=>"0"),array( Name=>"extra13", Display_name=>"", readonly=>"0", active=>"0"),array( Name=>"extra14", Display_name=>"", readonly=>"0", active=>"0"),array( Name=>"extra15", Display_name=>"", readonly=>"0", active=>"0")));
                exit;
	}

	$rows = array();
	while($r = mysql_fetch_assoc($sth)) {
	    $rows[] = $r;
	}
	
	echo __json_encode($rows);
        
        
?>