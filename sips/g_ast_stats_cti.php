<?php
############################################################################################
####  Name:             g_ast_stats_cti.php                                             ####
####  Version:          2.0                                                             ####
####  Copyright:        GOAutoDial Inc. - Januarius Manipol <januarius@goautodial.com>  ####
####  License:          AGPLv2                                                          ####
############################################################################################
require("includes/g_authenticate.php");
require("includes/g_hpage.php");
if 	(isset($_GET["status_type"]))			{$status_type=$_GET["status_type"];}
	elseif 
	(isset($_POST["status_type"]))			{$status_type=$_POST["status_type"];}
if 	(isset($_GET["status"]))			{$status=$_GET["status"];}
	elseif 
	(isset($_POST["status"]))			{$status=$_POST["status"];}
#ASTERISK	
if ($status_type=="01"){
	$varhttpopen='g_ast_stats_logs.php?status_type=01';
	}
#MYSQLD	
if ($status_type=="02"){
	$varhttpopen='g_ast_stats_logs.php?status_type=02';
	}	
#APACHE	
if ($status_type=="03"){
	$varhttpopen='g_ast_stats_logs.php?status_type=03';
	}	
#NETWORK
if ($status_type=="04"){
	$varhttpopen='g_ast_stats_logs.php?status_type=04';
	}
#NETWORK TRAFFIC
if ($status_type=="05"){
	$varhttpopen='if_eth0_goautodial.php';
	}
#ALL SYSTEM
if ($status_type=="ALL"){
	$varhttpopen='g_ast_stats_logs.php?status_type=ALL';
	}
?>
<html>                                                                      
<head>       
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>goaddons v1.0 © by  | GOAutoDial Inc.</title>                                                              
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
     http.open('get', '<? echo $varhttpopen; ?>'); 
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
                setTimeout(update,5000);                                    
            }                                                                                                                                       
        }                                                                   
    }                                                                                                                                         
    function update() {                                                     
        sendRequest();                                                      
    }                                                                       
</script>
<link href="csslib/gadi_content.css" rel="stylesheet" type="text/css">
</head> 
<?
echo "<body onLoad='return sendRequest();' />\n";
?>
<div id="webtemp21">
<div id="webtemp19">
<?require("g_menu.php");?>
</div>
<br>
<div id="webtemp20">
</div>
<br>
<pre>                                        
<span id="log" name="log"></span>
</pre>    
</div>
</body>                                                                     
</html>                                                                     