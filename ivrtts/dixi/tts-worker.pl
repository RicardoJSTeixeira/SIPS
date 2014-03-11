#!/usr/bin/perl
#

$PATHconf =		'/etc/astguiclient.conf';

open(conf, "$PATHconf") || die "can't open $PATHconf: $!\n";
@conf = <conf>;
close(conf);
$i=0;
foreach(@conf)
	{
	$line = $conf[$i];
	$line =~ s/ |>|\n|\r|\t|\#.*|;.*//gi;
	if ( ($line =~ /^PATHhome/) && ($CLIhome < 1) )
		{$PATHhome = $line;   $PATHhome =~ s/.*=//gi;}
	if ( ($line =~ /^PATHlogs/) && ($CLIlogs < 1) )
		{$PATHlogs = $line;   $PATHlogs =~ s/.*=//gi;}
	if ( ($line =~ /^PATHagi/) && ($CLIagi < 1) )
		{$PATHagi = $line;   $PATHagi =~ s/.*=//gi;}
	if ( ($line =~ /^PATHweb/) && ($CLIweb < 1) )
		{$PATHweb = $line;   $PATHweb =~ s/.*=//gi;}
	if ( ($line =~ /^PATHsounds/) && ($CLIsounds < 1) )
		{$PATHsounds = $line;   $PATHsounds =~ s/.*=//gi;}
	if ( ($line =~ /^PATHmonitor/) && ($CLImonitor < 1) )
		{$PATHmonitor = $line;   $PATHmonitor =~ s/.*=//gi;}
	if ( ($line =~ /^VARserver_ip/) && ($CLIserver_ip < 1) )
		{$VARserver_ip = $line;   $VARserver_ip =~ s/.*=//gi;}
	if ( ($line =~ /^VARDB_server/) && ($CLIDB_server < 1) )
		{$VARDB_server = $line;   $VARDB_server =~ s/.*=//gi;} 
	if ( ($line =~ /^VARDB_database/) && ($CLIDB_database < 1) )
		{$VARDB_database = $line;   $VARDB_database =~ s/.*=//gi;}
	if ( ($line =~ /^VARDB_user/) && ($CLIDB_user < 1) )
		{$VARDB_user = $line;   $VARDB_user =~ s/.*=//gi;}
	if ( ($line =~ /^VARDB_pass/) && ($CLIDB_pass < 1) )
		{$VARDB_pass = $line;   $VARDB_pass =~ s/.*=//gi;}
	if ( ($line =~ /^VARDB_port/) && ($CLIDB_port < 1) )
		{$VARDB_port = $line;   $VARDB_port =~ s/.*=//gi;}
	$i++;
	}

use DBI;
use Time::HiRes qw( usleep );
use OSSP::uuid; 

$dbhA = DBI->connect("DBI:mysql:$VARDB_database:$VARDB_server:$VARDB_port", "$VARDB_user", "$VARDB_pass")
    or die "Couldn't connect to database: " . DBI->errstr;

my $i = 1;

while ($i) {

$stmtA = "SELECT phone_number, lead_id, comments, email FROM vicidial_list where status='tts' limit 1;";
$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
$sthArows=$sthA->rows;
if ($AGILOG) {$agi_string = "$sthArows|$stmtA|";   &agi_output;}
if ($sthArows > 0)
	{
	@aryA = $sthA->fetchrow_array;
	$phone_number = $aryA[0];
	$lead_id = $aryA[1];
        $messageOne = $aryA[2];
        $messageTwo = $aryA[3];
	$sthA->finish();
        
        tie my $uuid, 'OSSP::uuid::tie';
        $uuid = [ "v1" ];
        $file_one = "/srv/www/htdocs/ivrtts/dixi/files/$uuid.mp3";          
        $file_two = "/srv/www/htdocs/ivrtts/dixi/files/$uuid.mp3";         
        untie $uuid;
        print "Got new lead, working on it \n";
        print "Converting message one: python /usr/share/Dixi/tts.py Vicente '$messageOne' > $file_one \n";
        print "Converting message two: python /usr/share/Dixi/tts.py Vicente '$messageTwo' > $file_one \n";

        system("python /usr/share/Dixi/tts.py Vicente '$messageOne' > $file_one; python /usr/share/Dixi/tts.py Vicente '$messageTwo' > $file_two; chmod +x $file_one; chmod +x $file_two");
        
        $stmtA = "UPDATE vicidial_list set extra2 = '$file_one', extra3 = '$file_two', status='NEW' where lead_id = $lead_id";
        $sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
        $sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
        print "My work is done \n";
       

	} else {
            # não encontrou nada, dorme 10 segs
            print "No leads found for TTS processing... sleeping for now \n";
            usleep (100000*100);
        }

}








