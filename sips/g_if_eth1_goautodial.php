<?php
############################################################################################
####  Name:             g_if_eth1_goautodial.php                                        ####
####  Version:          2.0                                                             ####
####  Copyright:        GOAutoDial Inc. - Januarius Manipol <januarius@goautodial.com>  ####
####  License:          AGPLv2                                                          ####
############################################################################################

require("includes/g_authenticate.php");
require("includes/g_hpage.php");

$s='&nbsp;';
$a='&raquo;';
?>
<div class='myfontb'><?echo $a;$s;?> ETH1&nbsp;NETWORK&nbsp;TRAFFIC:</div>
<br>
<?
echo "<div>\n";
echo "<table>\n";
    echo "<tr>\n";
      echo "<td><img src='munin/localdomain/localhost.localdomain-if_eth1-day.png' alt='daily graph' width='470'  height='269'/></td>\n";
      echo "<td><img src='munin/localdomain/localhost.localdomain-if_eth1-week.png' alt='weekly graph' width='470'  height='269'/></td>\n";
    echo "</tr>\n";
    echo "<tr>\n";
      echo "<td><img src='munin/localdomain/localhost.localdomain-if_eth1-month.png' alt='monthly graph' width='470'  height='269'/></td>\n";
      echo "<td><img src='munin/localdomain/localhost.localdomain-if_eth1-year.png' alt='yearly graph' width='470'  height='269'/></td>\n";
    echo "</tr>\n";
echo "</table>\n";
echo "</div>\n";
?>
<br>