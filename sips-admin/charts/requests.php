<?
date_default_timezone_set('Europe/Lisbon');

require("../../ini/_json_convert.php");
require("../../ini/dbconnect.php");
require("../../ini/functions.php");
foreach ($_POST as $key => $value) {
	${$key} = $value;
}
foreach ($_GET as $key => $value) {
	${$key} = $value;
}


if ($action == 'log-chamadas-resumo') {
	
	$Campaign = explode(",", $Campaign);
	
	
		
	
	$sQuery = "SELECT SUM( campaign_id = '$Campaign[0]' ) as result_one,
						SUM( campaign_id = '$Campaign[1]' ) as result_two,
						SUM( campaign_id = '$Campaign[2]' ) as result_three
				FROM vicidial_log
				WHERE call_date >= '$MinDate'
				AND call_date <= '$MaxDate'";
	
	$rResult = mysql_query( $sQuery, $link ) or die(mysql_error());
	$output = array("aaData" => array()	);
	

	$aColumns = array('result_one', 'result_two', 'result_three');
	
	while ( $aRow = mysql_fetch_array( $rResult ) )
	{
		$row = array();
		for ( $i=0 ; $i<count($aColumns) ; $i++ )
		{
		
		switch($aColumns[$i]) 
		{ 
			default: $row[] = $aRow[ $aColumns[$i] ];
		}
	}
	$output['aaData'][] = $row;
	}
	echo __json_encode( $output );
}

?>