<?php
############################################################################################
####  Name:             g_ast_send.php                                                  ####
####  Version:          2.0                                                             ####
####  Copyright:        GOAutoDial Inc. - Januarius Manipol <januarius@goautodial.com>  ####
####  License:          AGPLv2                                                          ####
############################################################################################

require("includes/g_authenticate.php");
require("includes/g_hpage.php");

if (isset($_GET['file'])) {
    $fname = $_GET['file'];
    $bname = basename($fname);
    $ext = substr($bname, strrpos($bname, '.') + 1);
    header("Content-type: application/$ext");
    header("Content-Disposition: attachment; filename=\"$bname\"");
    $file = fopen($fname, 'r');
    if ($file == FALSE) {
      echo "<BR>Error opening $fname<BR>";
      echo "Make sure the web server has authority to save this file!<BR>";
      exit();
    }
    $contents = fread($file, filesize ($fname));
    echo $contents;
    fclose($file);
}
    exit();
?>
