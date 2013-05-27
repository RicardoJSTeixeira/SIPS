<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>SIPS - Sistemas de Informação da PuroSinónimo</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link href="../css/style.css" rel="stylesheet" type="text/css" />
</head>
<body>

<?php
############################################################################################
####  Name:             g_ast_dir.php                                                   ####
####  Version:          2.0                                                             ####
####  Copyright:        GOAutoDial Inc. - Januarius Manipol <januarius@goautodial.com>  ####
####  License:          AGPLv2                                                          ####
############################################################################################

require("includes/g_authenticate.php");
require("includes/g_hpage.php");
require('g_files_list.php');


if (isset($_POST['path'])) $path = $_POST['path'];
else {
    if (isset($_GET['path'])) $path = $_GET['path'];
    else $path = '/etc/asterisk'; //$ASTCONFIGPATH;
}
if (isset($_POST['directory'])) $directory = $_POST['directory'];
else                            $directory = "";
if ($directory != "") $path = $directory;

if (isset($_GET['edit'])) $edit = $_GET['edit'];
else $edit = "yes";
if (isset($_POST['edit'])) $edit = $_GET['edit'];

	echo "<div class=cc-mstyle>";

	echo "<table>";
	echo "<tr>";
	echo "<td id='icon32'><img src='../images/icons/document_editing_32.png' /></td>";
	echo "<td id='submenu-title'> Editar Ficheiros de Configuração</td>";
	echo "<td style='text-align:right'><img src='../images/icons/error_16.png' /><i>  ( ATENÇÃO: apenas deve ser usado por um super administrador! )</i></td>";
	echo "</tr>";
	echo "</table>";
	echo "</div>";

 	echo "<br><br><SIZE=4><i> <center>Conteúdo da directoria dos ficheiros de configuração: <b>$path</b></i><br><br>\n\n";
	
	echo "<br>";
	echo "<div class=cc-mstyle>";
	echo "<table border = 0>\n";
	echo "<tr>";
	#echo "<th>#</th>";
	echo "<th colspan = 2>Nome do ficheiro</th>";
	echo "<th>Editar</th>\n";
	echo "<th>Última modificação</th>\n";
	echo "</tr>\n";

/*	
 *  $o=0;
	while ($dids_to_print > $o) 
		{
		$row=mysql_fetch_row($rslt);

		#echo "<tr $bgcolor><td><a href=\"$PHP_SELF?ADD=3311&did_id=$row[0]\">$row[0]</a></td>";
		echo "<td> $row[1]</td>";
		echo "<td> $row[2]</td>";
		echo "<td> $row[3]</td>";
		echo "<td><a href=\"$PHP_SELF?ADD=3311&did_id=$row[0]\"><img src='../images/icons/livejournal.png' /></a></td></tr>\n";
		$o++;
		}
*/

$handle = opendir($path) or die("<br><br>Não foi possível abrir a directoria: $path!<br /><br>Verifique se tem permissões de acesso à directoria.<br />");
$numfiles = 0;
$numdirs  = 0;
if (substr($path,-1,1) == '/') $path = substr($path,0,-1);

clearstatcache();

$file_array = Array();

while ($file = readdir($handle))  $file_array[] = $file;
closedir($handle);

sort ($file_array);
reset ($file_array);

//echo "<table border=1>";
while (list ($key, $val) = each ($file_array))
{
  if($val =="." || $val == "..") continue;
  echo "<tr>";
  $fname = $path . '/' . $val;
  if(is_dir($fname)) {
    $numdirs += 1;
   # echo "<td><a href=g_ast_dir.php?path=$fname&edit=$edit>[dir]&nbsp;$val</a></td>";
    echo "<td style='text-indent: 10px;'><img src='../images/icons/folder_brick_32.png'/></td> <td style=text-align:left;>$val</td>";  
    echo "<td><a href=g_ast_edit.php?file=$fname><img src='../images/icons/folder_edit_32.png' /></a></td>";
    
    # For detele function for future use
    #echo "<td><a href=g_ast_del.php?path=$fname>Delete</a></td>";
    
  }
  else {
    $numfiles +=1;
    if (strtolower($edit) == "yes") 
     	echo "<td style='text-indent: 10px;'><img src='../images/icons/directory_listing_32.png' /></td> <td style=text-align:left;>$val</td>"; 
     	#echo "<td><a href=g_ast_edit.php?file=$fname>$val</a></td>";
    else 
    	echo "<td style='text-align:left'><a href=g_ast_send.php?file=$fname>$val</a></td>";
    
    
    echo "<td><a href=g_ast_edit.php?file=$fname><img src='../images/icons/livejournal_32.png' /></a></td>";
    echo "<td  align=left style='font-size: 12px;'><FONT FACE='VERDANA' COLOR=BLACK SIZE=2><i>".date ("Y-m-d H:i:s", filemtime($fname))."</i></td>";
    
    # For detele function for future use    
    #echo "<td><a href=g_ast_del.php?file=$fname>Delete</a></td>";
  }
  echo "</tr>";
}
echo "<tr>";
echo "<td align=center colspan = 5>&nbsp; </td>";
echo "<tr>";
echo "<td align=center colspan = 5> $numfiles ficheiros nesta directoria.</td>";
echo "<tr>";
echo "<td align=center colspan = 5> $numdirs sub-pastas nesta directoria.</td>";
echo "</tr>";
echo "</table>";
echo "</div>";
?>
</body>
</html>