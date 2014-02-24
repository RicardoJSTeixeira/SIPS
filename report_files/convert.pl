use strict;
use warnings;
use Encode qw( encode decode );

open(my $INFILE,  '<', $ARGV[0]) or die $!;
open(my $OUTFILE, '>', $ARGV[1]) or die $!;

while (my $utf8 = <$INFILE>) {
   my $code_points = decode('UTF-8', $utf8);
   my $cp1252 = encode('cp1252', $code_points);
   print $OUTFILE $cp1252;
}
