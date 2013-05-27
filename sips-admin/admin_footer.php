<table border="0" width="100%" cellpadding="0" cellspacing="0" style="margin-top:2px;z-index:1" id="footer">
<tr><td<?php if ($short_header && (!eregi("AST_CLOSERs",$_SERVER['PHP_SELF']))) { echo ' height="200px"'; } ?>></td></tr>
<tr><td id="footertext">
 <p>GoAutoDial comes with no guarantees or warranties of any sorts, either written or implied. The Distribution is released as GPL.<br />Individual packages in the distribution come with their own licences.</p>
 <p><a href="http://goautodial.com" title="GOAutoDial Inc.- Empowering The Next Generation Contact Center" target="_blank">&copy; 2010 GOAutoDial, Inc.</a>  |  <a  href="../termsofuse.php" title="GOAutoDial - Terms Of Use" target="_blank">Terms of Use</a></p>
</td></tr>
</table>
</div>
<?php
if (($ADD==31 && isset($SUB)) || $ADD==3111111 || $ADD==3111 || $ADD==331111111 || $ADD==311111111 || $ADD==551)
	{
		echo "<script>document.getElementById('footer').style.position='absolute';document.getElementById('footer').style.top=(document.getElementById('contents').offsetTop+document.getElementById('contents').offsetHeight);</script>";
	}
?>