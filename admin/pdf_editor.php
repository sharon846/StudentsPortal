<?php

if (!isset($_GET['filename']))
    exit();

header("Content-type: application/pdf");
header("Content-Disposition: inline; filename=filename.pdf");
readfile($_GET['filename']);

?>