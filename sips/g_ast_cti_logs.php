<?php
############################################################################################
####  Name:             g_ast_cti_logs.php                                              ####
####  Version:          2.0                                                             ####
####  Copyright:        GOAutoDial Inc. - Januarius Manipol <januarius@goautodial.com>  ####
####  License:          AGPLv2                                                          ####
############################################################################################

require("includes/g_authenticate.php");
require("includes/g_hpage.php");
$logs = system('tail -n 100 /var/log/asterisk/messages');
?>                                                                                                       
