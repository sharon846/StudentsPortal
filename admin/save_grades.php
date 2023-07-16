<?php
require_once '../site_manager/pdoconfig.php';

function year_translate($year){
    $ar = explode(' ', jdtojewish(gregoriantojd(1, 1, $year), true, CAL_JEWISH_ADD_ALAFIM));
    return iconv ('WINDOWS-1255', 'UTF-8', end($ar));
}

if (isset($_POST['year'],$_POST['semester'],$_POST['course'],$_POST['lecture'])){
    
    try {
        $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    } catch (PDOException $pe) {
        die("Could not connect to the database $dbname :" . $pe->getMessage());
    }
    
    $year = year_translate($_POST['year']);
    
    if (isset($_POST['cmd']) && $_POST['cmd'] == "delete"){
        $sql = "DELETE TgradesSemesters, Tgrades FROM TgradesSemesters JOIN Tgrades ON TgradesSemesters.id = Tgrades.idsemester WHERE TgradesSemesters.name = 'name1' AND TgradesSemesters.lecture = 'lecture1' AND TgradesSemesters.year = 'year1' AND TgradesSemesters.semester = 'semester1';";
        $sql = str_replace('year1', $year, $sql);
        $sql = str_replace('semester1', $_POST['semester'], $sql);
        $sql = str_replace('name1', $_POST['course'], $sql);
        $sql = str_replace('lecture1', $_POST["lecture"], $sql);
        $res = $conn->query($sql);
        exit();
    }
    
    $sql1 = "INSERT INTO `Tgrades` (`idsemester`, `moed`, `avg`, `num`, `grades`, `proj2`) VALUES ((SELECT `id` FROM `TgradesSemesters` WHERE `name`='name1' AND `lecture`='lecture1' AND `year`='year1' AND `semester`='semester1'), 'moed1', avg1, num1, 'grades1', NULL) ON DUPLICATE KEY UPDATE `avg`='avg1', `num`='num1', `grades`='grades1', `proj2`=NULL;";
    $sql2 = "SELECT * FROM `Tgrades`,TgradesSemesters WHERE `idsemester` IN (SELECT id from TgradesSemesters WHERE lecture='lecture1' AND name='name1' AND year='year1' AND semester='semester1') AND TgradesSemesters.id=Tgrades.idsemester ORDER BY Tgrades.moed ASC;";

    $sql = isset($_POST['data']) ? $sql1 : $sql2;
    
    $sql = str_replace('year1', $year, $sql);
    $sql = str_replace('semester1', $_POST['semester'], $sql);
    $sql = str_replace('name1', $_POST['course'], $sql);
    $sql = str_replace('lecture1', $_POST["lecture"], $sql);
    $cmd = $sql;
    
    $cmds = array();
    $res = "";
    
    if (isset($_POST['data'])){
        
        $moeds = json_decode($_POST['data'], true);
        
        $sql3 = "INSERT INTO `TgradesSemesters`(`name`, `lecture`, `year`, `semester`, `proj`) VALUES ('name1','lecture1','year1','semester1',NULL) ON DUPLICATE KEY UPDATE `proj`=NULL;";
        
        $sql3 = str_replace('year1', $year, $sql3);
        $sql3 = str_replace('semester1', $_POST['semester'], $sql3);
        $sql3 = str_replace('name1', $_POST['course'], $sql3);
        $sql3 = str_replace('lecture1', $_POST["lecture"], $sql3);
        if ($_POST["proj"] != "") $sql3 = str_replace('NULL', "'".$_POST['proj']."'", $sql3);
        $conn->query($sql3);
        
        foreach ($moeds as $moed)
        {
            $cmd = str_replace('moed1', $moed["moed"], $sql);
            $cmd = str_replace('avg1', $moed["avg"], $cmd);
            $cmd = str_replace('num1', $moed["num"], $cmd);
            $cmd = str_replace('grades1', $moed["grades"], $cmd);
            
            if ($moed["proj2"] != "") $cmd = str_replace('NULL', "'".$moed["proj2"]."'", $cmd);
            $conn->query($cmd);
        }
    } 
    else{
        $result = $conn->query($cmd);
        $res = $result->fetchAll();
        $res = json_encode($res);
    }

    echo $res;
}
exit();


?>