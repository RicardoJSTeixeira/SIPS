<?php
############################################################################################
####  Name:             g_ast_cti.php                                                   ####
####  Version:          2.0                                                             ####
####  Copyright:        GOAutoDial Inc. - Januarius Manipol <januarius@goautodial.com>  ####
####  License:          AGPLv2                                                          ####
############################################################################################

require("includes/g_authenticate.php");
require("includes/g_hpage.php");

if 	(isset($_GET["status"]))			{$status=$_GET["status"];}
	elseif 
	(isset($_POST["status"]))			{$status=$_POST["status"];}
?>
<html>                                                                      
<head>                                                                      
<script type="text/javascript">                                             
    function createRequestObject() {                                        
                                                                            
        var req;                                                            
                                                                            
        if(window.XMLHttpRequest){                                          
            // Firefox, Safari, Opera...                                    
            req = new XMLHttpRequest();                                     
        } else if(window.ActiveXObject) {                                   
            // Internet Explorer 5+                                         
            req = new ActiveXObject("Microsoft.XMLHTTP");                   
        } else {                                                            
            // There is an error creating the object,                       
            // just as an old browser is being used.                        
            alert('There was a problem creating the XMLHttpRequest object');
        }                                                                   
                                                                            
        return req;                                                         
                                                                          
    }                                                                       
                                                                            
    // Make the XMLHttpRequest object                                       
    var http = createRequestObject();                                       
                                                                            
    function sendRequest() {                                                
                                                                            
        // Open PHP script for requests                                     
		//http.open('get', 'email-delayed.php?bustcache='+new Date().getTime());    
     http.open('get', 'g_ast_cti_logs.php');    
                 
        http.onreadystatechange = handleResponse;                           
        http.send(null);                                                    
                                                                            
    }    
    
            function stopRequest() {                                                
                                                                            
        // Open PHP script for requests                                     
     	//
        //http.open('get', 'g_ast_cti_logs.php');                
                                              http.abort();                                                                            
                                              return;
    } 
                                                                       
                                                                            
    function handleResponse() {                                             
                                                                            
        if(http.readyState == 4 && http.status == 200){                     
                                                                            
            // Text returned FROM PHP script                                
            var response = http.responseText;                               
                                                                            
            if(response) {                                                  
                // UPDATE ajaxTest content                                  
                document.getElementById("log").innerHTML = response;        
                setTimeout(update,1000);                                    
            }                                                               
                                                                            
        }                                                                   
    }                                                                       
                                                                            
    function update() {                                                     
        sendRequest();                                                      
    }                                                                       
</script>                                                                   
</head>                                                                     
<?
if ($status=='on')
	{
		echo "<body onLoad='return sendRequest();' />\n";
		#echo "ONNNNNNNN";
?>
<pre>                                           
<span id="log" name="log"></span>
</pre>
<?
	}
	else
	{
		echo "<body onLoad='return stopRequest();' />\n";
		#echo "OFFFFFFFFF";
		echo "<font size=1>&nbsp;</font>";
		echo "<pre>\n";                                                                       
		$logs = system('tail -n 100 /var/log/asterisk/messages');
		echo "<pre>\n"; 
	}
?>                                          
                                                                      
</body>                                                                     
</html>                                                                     