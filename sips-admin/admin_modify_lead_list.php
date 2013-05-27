<?php #HEADER
$self=count(explode('/', $_SERVER['PHP_SELF']));
for($i=0;$i<$self-2;$i++){$header.="../";} 
define("ROOT", $header);
require(ROOT."ini/header.php");
?>

<?php

$query = "SELECT phone_number, first_name, address1 FROM vicidial_list where list_id=103 limit 1000";
echo $query;
$query = mysql_query($query, $link) or die(mysql_error());

?>


<br><br><br>
<input type="button" onclick="DoIt();" value="TABLE">
<br><br><br>
<input id='ext' type="text" value='454545'>

<div id="table-cont" style='visibility:visible'>

<table id='lead_list'>
<thead></thead>
<tbody></tbody>
<tfoot></tfoot>
</table>

<div>


<script>

function DoIt(){  

		var valuet = $("#ext").val();
		

		$('#lead_list').dataTable( {
 		"bProcessing": true,
        "sAjaxSource": '../requests/admin_lead_modify_table.php',
		 "fnServerParams": function ( aoData ) {
            	aoData.push( { "name": "lead_id", "value": valuet },{ "name": "next", "value": "8947" } ); 
			},
		 "aoColumns": [ { "sTitle": "Nome"}, { "sTitle": "sdfassdf"}, { "sTitle": "sdfsd"} ]
		
		
		 } );
		
	$('#table-cont').css({"visibility":"visible"});

	
}


$(document).ready(function() {

    //$('#lead_list').dataTable()
           
        


       
} ); 
</script>
<?php #FOOTER
$footer=ROOT."ini/footer.php";
require($footer);
?>