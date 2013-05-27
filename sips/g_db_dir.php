<?php
############################################################################################
####  Name:             g_net_dir.php                                                   ####
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
    else $path = $GODBBACKUPDIR;
}
if (isset($_POST['directory'])) $directory = $_POST['directory'];
else                            $directory = "";
if ($directory != "") $path = $directory;

if (isset($_GET['edit'])) $edit = $_GET['edit'];
else $edit = "yes";
if (isset($_POST['edit'])) $edit = $_GET['edit'];

$PHP_SELF=$_SERVER['PHP_SELF'];
$download_path="gosysbackup";

$handle = opendir($path) or die("<br><br><FONT FACE='VERDANA' COLOR=BLACK SIZE=2>Unable to open directory $path!<br /><br>Please make sure the directory exits with proper authority!<br />");


if ($path=='/usr/share/goautodial/gosysbackup'){


echo "<div>\n";
echo "<br><br><FONT FACE='VERDANA' COLOR=BLACK SIZE=2><b><i>Directory of $path</i></b><br><br>\n";

$numfiles = 0;
$numdirs  = 0;
if (substr($path,-1,1) == '/') $path = substr($path,0,-1);

clearstatcache();

$file_array = Array();

while ($file = readdir($handle))  $file_array[] = $file;
closedir($handle);

sort ($file_array);
reset ($file_array);

echo "<table border=0>";
while (list ($key, $val) = each ($file_array))
{
  if($val =="." || $val == "..") continue;
  
  if (preg_match("/^/", $val)) {
                 
  
  
  echo "<tr>";
  $fname = $path . '/' . $val;
  if(is_dir($fname)) {
    $numdirs += 1;
   # echo "<td><a href=g_ast_dir.php?path=$fname&edit=$edit>[dir]&nbsp;$val</a></td>";
    echo "<td colspan=0 align=left style='font-size: 12px;'>&nbsp;</td>";
    echo "<td colspan=0 align=left style='font-size: 12px;'><FONT FACE='VERDANA' COLOR=BLACK SIZE=2><a href=g_ast_edit.php?path=$fname>Edit</a></td>";
    
    # For detele function for future use
    #echo "<td><a href=g_ast_del.php?path=$fname>Delete</a></td>";
    
  }
  else {
    $numfiles +=1;
    if (strtolower($edit) == "yes") 
     echo "<td colspan=0 align=left style='font-size: 12px;'><FONT FACE='VERDANA' COLOR=BLACK SIZE=2>$val</td>"; 
    #echo "<td><a href=g_ast_edit.php?file=$fname>$val</a></td>";
    else 
    echo "<td colspan=0 align=left style='font-size: 12px;'><FONT FACE='VERDANA' COLOR=BLACK SIZE=2><a href=g_ast_send.php?file=$fname>$val</a></td>";
    echo "<td colspan=0 align=left style='font-size: 12px;'><FONT FACE='VERDANA' COLOR=BLACK SIZE=2>&nbsp;</td>";
    
    $location = '../'.$download_path.'/'.$val.'';
    
    echo "<td colspan=0 align=left style='font-size: 12px;'><FONT FACE='VERDANA' COLOR=BLACK SIZE=2><a href='$location'>Download</a></td>";
    echo "<td colspan=0 align=left style='font-size: 12px;'><FONT FACE='VERDANA' COLOR=BLACK SIZE=2>&nbsp;</td>";
    echo "<td colspan=0 align=left style='font-size: 12px;'><FONT FACE='VERDANA' COLOR=BLACK SIZE=2><i>".date("Y-m-d H:i:s", filemtime($fname))."</i></td>";
    
    # For detele function for future use    
    #echo "<td><a href=g_ast_del.php?file=$fname>Delete</a></td>";
  }
  echo "</tr>";
}
}
echo "</table>";
echo "<br><b><FONT FACE='VERDANA' COLOR=BLACK SIZE=2>";
echo $numfiles.' files in this directory';
echo "<br><FONT FACE='VERDANA' COLOR=BLACK SIZE=2>";
echo $numdirs.' subdirectories in this directory';
echo "</div>";
}
?>
