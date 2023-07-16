<?php

$files = glob(getcwd()."/*.log");
foreach ($files as $file)
    unlink($file);
?>