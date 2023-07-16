<?php

if (!isset($_GET['filename']))
    exit();

$imginfo = getimagesize($_GET['filename']);
header("Content-type: {$imginfo['mime']}");
readfile($_GET['filename']);

?>