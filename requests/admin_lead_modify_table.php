<?php
require("_json_convert.php");
require("../ini/dbconnect.php");

$aColumns = array( 'first_name', 'phone_number', 'address1');

$sQuery = "
		SELECT first_name, phone_number, address1 
		FROM   vicidial_list
		WHERE list_id=103
		LIMIT 1000

		";
	$rResult = mysql_query( $sQuery, $link ) or die(mysql_error());

	$output = array(

		"aaData" => array()
	);
	
	while ( $aRow = mysql_fetch_array( $rResult ) )
	{
		$row = array();
		for ( $i=0 ; $i<count($aColumns) ; $i++ )
		{
			
				$row[] = $aRow[ $aColumns[$i] ];
			
		}
		$output['aaData'][] = $row;
	}
	
	echo __json_encode( $output );
	


?>