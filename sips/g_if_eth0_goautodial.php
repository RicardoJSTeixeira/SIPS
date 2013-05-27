<?php
############################################################################################
####  Name:             g_if_eth0_goautodial.php                                        ####
####  Version:          2.0                                                             ####
####  Copyright:        GOAutoDial Inc. - Januarius Manipol <januarius@goautodial.com>  ####
####  License:          AGPLv2                                                          ####
############################################################################################

require("includes/g_authenticate.php");
require("includes/g_hpage.php");

$s='&nbsp;';
$a='&raquo;';
?>
<div class='myfontb'><?echo $a;$s;?> ETH0&nbsp;NETWORK&nbsp;TRAFFIC:</div>
<br>
<?
echo "<div>\n";
echo "<table>\n";
    echo "<tr>\n";
      echo "<td><img src='munin/localdomain/localhost.localdomain-if_eth0-day.png' alt='daily graph' width='470'  height='269'/></td>\n";
      echo "<td><img src='munin/localdomain/localhost.localdomain-if_eth0-week.png' alt='weekly graph' width='470'  height='269'/></td>\n";
    echo "</tr>\n";
    echo "<tr>\n";
      echo "<td><img src='munin/localdomain/localhost.localdomain-if_eth0-month.png' alt='monthly graph' width='470'  height='269'/></td>\n";
      echo "<td><img src='munin/localdomain/localhost.localdomain-if_eth0-year.png' alt='yearly graph' width='470'  height='269'/></td>\n";
    echo "</tr>\n";
echo "</table>\n";
echo "</div>\n";
?>
<br>