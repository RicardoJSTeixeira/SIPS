<?php
############################################################################################
####  Name:             g_ast_del.php                                                   ####
####  Version:          2.0                                                             ####
####  Copyright:        GOAutoDial Inc. - Januarius Manipol <januarius@goautodial.com>  ####
####  License:          AGPLv2                                                          ####
############################################################################################

require("includes/g_authenticate.php");
require("includes/g_hpage.php");

$file = "";
$path = "";

if (isset($_POST['file'])) $file = $_POST['file'];
if (isset($_GET['file']))  $file = $_GET['file'];

if ($file != "") {
 if (file_exists($file)) $rc = unlink($file);
 if ($rc)  echo "<br>" . $file . " deleted!<BR>";
 else echo "An error occured in deletion. Make sure the proper permissions are in place.<BR>";
}

if (isset($_POST['path'])) $path = $_POST['path'];
if (isset($_GET['path']))  $path = $_GET['path'];

if ($path != "") {
 if (file_exists($path)) $rc = unlink($path);
 if ($rc)  echo "<br>" . $path . " deleted!<BR>";
 else echo "An error occured in deletion. Make sure the proper permissions are in place.<BR>";
}
?>