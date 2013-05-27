<?php
################################################################################
####  Name:			g_files_list.php				        				####
####  Version:      2.0	                                       				####
####  Copyright: 	GOAutoDial Inc. 2010 - Januarius Manipol				####
####  License:		GPLv2													####
################################################################################

require("includes/g_authenticate.php");

$ASTCONFIGPATH  = "/etc/asterisk";
$ASTLIBDIR      = "/var/lib/asterisk";
$ASTSPOOLDIR    = "/var/spool/asterisk";
$ASTLOGDIR      = "/var/log/asterisk";
$SYSTEMMESSAGES = "/var/log/messages";
$ASTEVENTLOG    = "/var/log/asterisk/event_log";
$ASTMESSAGESLOG = "/var/log/asterisk/messages";
$ASTQUEUELOG    = "/var/log/asterisk/queue_log";
$ASTOUTGOINGDIR = "/var/spool/asterisk/outgoing";
$NICDIR1	= "/etc/sysconfig/network-scripts";
$NICDIR2	= "/etc/sysconfig";
$ETCDIR		= "/etc";
$GODBBACKUPDIR  = "/usr/share/goautodial/gosysbackup";
$TMPDIR         = "/tmp/";
?>
