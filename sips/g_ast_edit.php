<?php
############################################################################################
####  Name:             g_ast_edit.php                                                  ####
####  Version:          2.0                                                             ####
####  Copyright:        GOAutoDial Inc. - Januarius Manipol <januarius@goautodial.com>  ####
####  License:          AGPLv2                                                          ####
############################################################################################

require("includes/g_authenticate.php");
require("includes/g_hpage.php");

 if (isset($_POST['submit'])) {
   if ($_POST['submit'] == "Download") {
      $fname = $_POST['file'];
      $bname = basename($fname);
      header("Content-type: application/txt");
      header("Content-Disposition: attachment; filename=\"$bname\"");
      $file = fopen($fname, 'r');
      if ($file == FALSE) {  
	  	echo "<br><br><FONT FACE='VERDANA' COLOR=BLACK SIZE=2>Error opening $fname!<br />\n";
		echo "<br><FONT FACE='VERDANA' COLOR=BLACK SIZE=2>Please make sure the web server is authorized to read this file!<br />\n";
        exit();
      }
      $contents = fread($file, filesize ($fname));
      echo $contents;
      fclose($file);
      exit();
   }
   if ($_POST['submit'] == "Save") {
      $data=$_POST['data'];
      $fname = $_POST['file'];
      $file = fopen($fname, 'w');
      if ($file != FALSE) {
        fwrite($file, $data);
        fclose($file);        
        #system("/usr/share/goautodial/g_parse.pl '$fname'");
        
        exec("/usr/share/goautodial/goautodialc.pl '/usr/bin/nohup /bin/chmod 777 $fname'");
       	exec("/usr/share/goautodial/goautodialc.pl '/usr/bin/nohup /usr/bin/dos2unix $fname'");	

       	echo "<BR><FONT FACE='VERDANA' COLOR=BLACK SIZE=2>Configuration file $fname was successfully saved!<BR>";
      }
      else {
	  	echo "<br><br><FONT FACE='VERDANA' COLOR=BLACK SIZE=2>Error saving $fname!<br />\n";
		echo "<br><FONT FACE='VERDANA' COLOR=BLACK SIZE=2>Please make sure the web server is authorized to save this file!<br />\n"; 
      }
      exit();
   }
 }
 $fname = $_GET['file'];
 $file = fopen($fname, 'r');
 if ($file == FALSE) {

	echo "<br><br><FONT FACE='VERDANA' COLOR=BLACK SIZE=2>Error opening $fname!<br />\n";
	echo "<br><FONT FACE='VERDANA' COLOR=BLACK SIZE=2>Please make sure the web server has authority to read this file!<br />\n"; 
	exit(0);
 }
 $contents = fread($file, filesize ($fname));
 fclose($file);

echo "<br><FONT FACE='VERDANA' COLOR=BLACK SIZE=2><b><i>Configuration file: $fname</i><br>\n";
?>
<center>
<form method="post" action="<?php $_SERVER['PHP_SELF']?>">
<input type="hidden" name="file"   value=<?php echo "$fname";?>>   </input>
<br><br>
<table border=1>
<TR><td>
<textarea name="data" cols=88 rows=23>
<?php echo htmlentities($contents); //require ($fname); ?>
</textarea>
</td></tr>
</table>
<br>
<input type="submit" name="submit" value="Save">   </input>
&nbsp;
<input value="Discard" onclick="location.href='./g_dead.php'" type="button">
&nbsp;
<input type="submit" name="submit" value="Download">   </input>
</form>
</center>