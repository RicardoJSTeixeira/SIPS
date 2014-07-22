<?php

if (
        !isset($_GET['file']) || ($f = base64_decode($_GET['file'])) === false || ($fp = @fopen($f, "rb")) === false || ($fi = pathinfo($f)) === false || ($fi['fsize'] = filesize($f)) === false || strtolower($fi["extension"]) != 'pdf'
)
    die('Failed');

ob_start();
header('Accept-Ranges: bytes');
header("Content-Length: {$fi['fsize']}");
header('Content-Type: application/pdf');
if (!isset($_GET['i']))
    header("Content-Disposition: attachment; filename='{$fi['basename']}'");

$sent = 0;
while (!feof($fp) && $sent < $fi['fsize'] && ($buf = fread($fp, 8192)) != '') {
    echo $buf;
    $sent += strlen($buf);
    flush();
    ob_flush();
}
fclose($fp);
exit;
