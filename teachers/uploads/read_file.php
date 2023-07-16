<?php

session_start();

if (!$_SESSION['_sfm_allowed'] || !isset($_SERVER["PATH_INFO"]))
{
        die();
}
// Store the file name into variable
setlocale(LC_ALL,'C.UTF-8');
$file = substr($_SERVER["PATH_INFO"], 1);
$filename = basename(substr($_SERVER["PATH_INFO"], 1));

if (substr(strtolower(pathinfo($filename, PATHINFO_EXTENSION)), 0, 3) === "php" || !file_exists($file))
{
        die();
}

// Header content type
header('Content-Type: ' . mime_content_type($filename));

header('Content-Length: ' . filesize($file));

header('Content-Disposition: inline; filename="' . $filename . '"');

header('Content-Transfer-Encoding: binary');

header('Accept-Ranges: bytes');

@readfile($file);

?>
