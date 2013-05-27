<?php
for ($i = 0; $i < count(explode('/', $_SERVER['PHP_SELF'])) - 2; $i++) {
    $far.="../";
}
define("ROOT", $far);
require(ROOT . "ini/header.php");
?>

        <center><div id="placeholder" style="width:95%;height:300px"></div></center>

        <script>

        /* ----------------------------------------------------------------------------------------------------------------- */

        $(function () {

                var options = {
        lines: { show: true },
        points: { show: true },
        xaxis: { tickDecimals: 0, tickSize: 1 }
    };

        var data = [];

        var placeholder = $("#placeholder");

        $.plot(placeholder, data, options)
        // fetch one series, adding to what we got
 var alreadyFetched = {};
 
        $.post("sips_xmlrequests.php",{ACTION:"get_graph_data"},function(data){$.plot(placeholder, [data.data], options);},"json");
        
        
        }); 

        </script>
