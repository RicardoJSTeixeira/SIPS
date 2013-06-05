#!/usr/bin/perl -
#
# SIPS Project
# This script is intended to update the lead status if the number is unalocated/unassigned
use DBI;
use DateTime;
my $database = 'asterisk';
my $server = '127.0.0.1';
my $port = '3306';
my $user = 'sipsadmin';
my $password = 'sipsps2012';

my $dbhA = DBI->connect("DBI:mysql:" . $database . ":" . $server . ":" . $port,
	$user, $password) or die "Couldn't connect to database: " . DBI->errstr;

### Grab Server values from the database




my $stmtA = "update vicidial_log a join vicidial_carrier_log b 
on a.uniqueid = b.uniqueid join vicidial_list c 
on a.lead_id = c.lead_id
set a.status = 'NAOEX', c.status = 'NAOEX'
where b.hangup_cause = '1' 
and c.status IN ('NAOEX', 'NA')
and char_length(c.alt_phone) < 4
and char_length(c.address3) < 4";

print "$stmtA \n";

my $sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
$sthA->finish();

$dbhA->disconnect();