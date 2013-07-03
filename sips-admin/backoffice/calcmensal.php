<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
</head>
<body>
<?
$whereGrp = "";
if (isset($_POST['datainicio'])) { $startDate = $_POST['datainicio']; }
if (isset($_POST['datafim'])) { $endDate = $_POST['datafim']; }
if (isset($_POST['usergroup'])) { $userGroup = $_POST['usergroup']; $whereGrp = "AND user_group LIKE '$userGroup'";}
?>
<form id='fchoose' target='_self' method='post' action='calcmensal.php'>
<label for='datainicio'>Data Inicio</label><input type='date' id='datainicio' name='datainicio' selected value='<? echo $startDate; ?>' />
<br>
<label for='datafim'>Data Fim</label><input type='date' id='datafim' name='datafim' value='<? echo $endDate; ?>' />
<br>
<label for='usergroup'>User Group (ID)</label><input type='text' id='usergroup' name='usergroup' value='<? echo $userGroup; ?>' />
<br>
<input type='submit' value='submit' />
<br>
</form>
<?php 
		$con = mysql_connect("localhost","sipsadmin", "sipsps2012");
				if (!$con)
				{
					die('Não me consegui ligar' . mysql_error());
				}
				mysql_select_db("asterisk", $con);
                                
		$dbusers = mysql_query("SELECT user, event, event_date, user_group user FROM vicidial_user_log WHERE event_date >= '$startDate' AND event_date < '$endDate' AND user <> 'admin' $whereGrp ORDER BY event_date ASC") or die(mysql_error());
		$loginArray = "";
		$maxUsers = 0;
		$perDayArray = "";
		$curDate = $startDate;
		for ($i=0;$i<mysql_num_rows($dbusers);$i++) {
			$curRow = mysql_fetch_row($dbusers);
			if (strtotime(date("Y-m-d",strtotime($curRow[2]))) > strtotime($curDate)) {
				$perDayArray[$curDate] = $maxUsers;
				echo "Dia: $curDate - Num Licencas: $maxUsers <br>";
				$maxUsers = 0;
				unset($loginArray);
			}
			if ($curRow[1] == "LOGIN") {
				if(!isset($loginArray[$curRow[0]])) {
					$loginArray[$curRow[0]] = 1;
					if (count($loginArray) > $maxUsers) {
						$maxUsers = count($loginArray); 
					}
				}
			} else if ($curRow[1] == "LOGOUT") {
				unset($loginArray[$curRow[0]]);
			}
			$curDate = date("Y-m-d",strtotime($curRow[2]));
		}
		$newArray = $perDayArray;
		arsort($newArray);
		$midVal = floor(count($newArray)*0.4);
		$a = 0;
		foreach ($newArray as $curKey) {
			$a++;
			$totalVal = $totalVal + $curKey;
			if ($a == $midVal) { break; }
		}
		$new_avg_value = ceil($totalVal / $midVal);
		echo "Valor Total: $totalVal // Valor medio: $new_avg_value (considerando o top 40% de utilização)";
?>
</body>
</html>
